<?php

namespace app\Http\Extensions\LayoutUser;
use Closure;
use Encore\Admin\Layout\Content;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\MessageBag;

class ContentUser extends Content
{
    public function render()
    {
        $items = [
            'header'      => $this->header,
            'description' => $this->description,
            'breadcrumb'  => $this->breadcrumb,
            'content'     => $this->build(),
        ];

        return view('User.content', $items)->render();
    }
}
