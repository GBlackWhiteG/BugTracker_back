<?php

namespace App\Http\Controllers;

use App\Enums\BugCriticality;
use App\Enums\BugPriority;
use App\Enums\BugStatus;
use App\Models\Bug;
use App\Models\BugFile;
use App\Models\BugHistory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class BugController extends Controller
{
    public function index(): JsonResponse
    {
        $bugs = Bug::with(['files'])->orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'data' => $bugs->items(),
            'pagination' => [
                'total' => $bugs->total(),
                'per_page' => $bugs->perPage(),
                'current_page' => $bugs->currentPage(),
                'next_page_url' => $bugs->nextPageUrl(),
            ],
        ]);
    }

    public function show(Bug $bug): JsonResponse
    {
        return response()->json([
            'data' => $bug->load(['files', 'comments']),
        ]);
    }

    public function store(): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'criticality' => [new Enum(BugCriticality::class)],
            'priority' => [new Enum(BugPriority::class)],
            'status' => [new Enum(BugStatus::class)],
            'steps' => 'required|string',
            'responsible_user_id' => 'required|integer|exists:users,id',
            'files' => 'array|max:9',
            'files.*' => 'mimes:jpg,jpeg,png,txt,log,pdf,zip|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $data['user_id'] = auth()->id();

        return DB::transaction(function () use ($data) {
            $bug = Bug::create($data);

            if (isset($data['files']) && is_array($data['files']) && count($data['files'])) {
                foreach ($data['files'] as $file) {
                    $file_path = $file->storeAs('files', uniqid() . '.' . $file->getClientOriginalExtension(), 'public');

                    $bug->files()->create([
                        'file_url' => asset('storage/' . $file_path),
                    ]);
                }
            }

            return response()->json(['data' => $bug->load('files')]);
        }, 3);
    }

    public function update(Bug $bug): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'steps' => 'required|string',
            'files' => 'array|max:9',
            'files.*' => 'mimes:jpg,jpeg,png,txt,log,pdf,zip|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        return DB::transaction(function () use ($data, $bug) {
            $bug->update($data);

            if (isset($data['files']) && is_array($data['files']) && count($data['files'])) {
                foreach ($data['files'] as $file) {
                    $file_path = $file->storeAs('files', uniqid() . '.' . $file->getClientOriginalExtension(), 'public');

                    $bug->files()->create([
                        'file_url' => asset('storage/' . $file_path),
                    ]);
                }
            }

            return response()->json(['data' => $bug->load('files')]);
        }, 3);
    }

    public function changeField(Bug $bug): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'change_field' => ['required', Rule::in(['status', 'priority', 'criticality', 'responsible_user_id'])],
            'new_value' => [
                'required',
                function ($attribute, $value, $fail) use ($bug) {
                    $change_field = request()->input('change_field');

                    switch ($change_field) {
                        case 'status':
                            if (!BugStatus::tryFrom($value)) {
                                $fail('Недоступное значение для поля "статус"');
                            }
                            break;
                        case 'priority':
                            if (!BugPriority::tryFrom($value)) {
                                $fail('Недоступное значение для поля "приоритет"');
                            }
                            break;
                        case 'criticality':
                            if (!BugCriticality::tryFrom($value)) {
                                $fail('Недоступное значение для поля "критичность"');
                            }
                            break;
                        case 'responsible_user_id':
                            if (!is_numeric($value) || !User::where('id', $value)->exists()) {
                                $fail('Пользователь не существует');
                            }
                            break;
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        return DB::transaction(function () use ($bug, $data) {
            BugHistory::create([
                'bug_id' => $bug['id'],
                'user_id' => auth()->id(),
                'field' => $data['change_field'],
                'old_value' => $bug->{$data['change_field']},
                'new_value' => $data['new_value'],
            ]);

            $bug->update([$data['change_field'] => $data['new_value']]);

            return response()->json(['data' => [$bug]]);
        }, 3);
    }

    public function destroy(Bug $bug): JsonResponse
    {
        foreach ($bug->files as $file) {
            Storage::delete($file->file_url);
        }

        $bug->files()->delete();
        $bug->delete();

        return response()->json(['message' => 'Баг успешно удален']);
    }

    public function destroyFile(BugFile $file)
    {
        $file->delete();

        return response()->json(['message' => "Файл успешно удален"]);
    }
}
