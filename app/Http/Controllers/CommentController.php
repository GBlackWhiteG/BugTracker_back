<?php

namespace App\Http\Controllers;

use App\Models\Bug;

use App\Models\Comment;
use App\Models\CommentFile;
use App\Models\User;
use App\Notifications\MentionNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'bug_id' => 'required|integer|exists:bugs,id',
            'comment' => 'required|string',
            'files' => 'array|max:9',
            'files.*' => 'mimes:jpg,jpeg,png,txt,log,pdf,zip|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        return DB::transaction(function () use ($data) {
            $data['user_id'] = auth()->id();
            $data['comment'] = $this->formatMentions($data['comment']);

            $comment = Comment::create($data);

            if (isset($data['files']) && is_array($data['files']) && count($data['files'])) {
                foreach ($data['files'] as $file) {
                    $filepath = $file->storeAs('files', uniqid() . '.' . $file->getClientOriginalExtension(), 'public');

                    $comment->files()->create([
                        'file_url' => asset('storage/' . $filepath),
                    ]);
                }
            }

            $mentionedUsers = $this->getMentionedUsers($comment->comment);
            foreach ($mentionedUsers as $user) {
                $user->notify(new MentionNotification($comment));
            }

            return response()->json(['data' => $comment->load('files')]);
        }, 3);
    }

    private function getMentionedUsers($content)
    {
        preg_match_all('/@([a-zA-Z0-9_]+)/', $content, $matches);

        return User::whereIn('nickname', $matches[1])->get();
    }

    private function formatMentions($content): array|string|null
    {
        return preg_replace_callback('/@([a-zA-Z0-9_]+)/', function ($matches) {
            $users = User::where('nickname', $matches[1])->get();
            $comment = '';
            foreach ($users as $user) {
                if ($comment !== '') $comment .= ', ';
                $comment .= "<a href='/profile/{$user->id}'>@{$user->nickname}</a>";
            }

            return $comment;
        }, $content);
    }

    public function update(Comment $comment): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'comment' => 'required|string',
            'files' => 'array|max:9',
            'files.*' => 'mimes:jpg,jpeg,png,txt,log,pdf,zip|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        return DB::transaction(function () use ($data, $comment) {
            $comment->update($data);

            if (isset($data['files']) && is_array($data['files']) && count($data['files'])) {
                foreach ($data['files'] as $file) {
                    $filepath = $file->storeAs('files', uniqid() . '.' . $file->getClientOriginalExtension(), 'public');

                    $comment->files()->create([
                        'file_url' => asset('storage/' . $filepath),
                    ]);
                }
            }

            return response()->json(['data' => $comment->load('files')]);
        }, 3);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        foreach ($comment->files as $file) {
            Storage::delete($file->file_url);
        }

        $comment->files()->delete();
        $comment->delete();

        return response()->json(['message' => 'Комментарий успешно удален']);
    }

    public function destroyFile(CommentFile $file): JsonResponse
    {
        $file->delete();

        return response()->json(['message' => "Файл успешно удален"]);
    }
}
