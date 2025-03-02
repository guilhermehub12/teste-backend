<?php

declare(strict_types = 1);

namespace App\Traits;

trait PostFiltered
{
    public function postDetailed($post): array
    {
        return [
            'id'     => $post->id,
            'title'  => $post->title,
            'author' => [
                'id'       => $post->user->id,
                'name'     => $post->user->name,
                'email'    => $post->user->email,
                'telefone' => $post->user->telefone,
            ],
            'content' => $post->content,
            'tags'    => $post->tags->pluck('name')->toArray(),
        ];
    }

    public function postSimple($post): array
    {
        return [
            'id'      => $post->id,
            'title'   => $post->title,
            'author'  => $post->author->nome,
            'content' => $post->content,
            'tags'    => $post->tags->pluck('name')->toArray(),
        ];
    }
}
