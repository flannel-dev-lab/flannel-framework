<?php

class Controller_Admin_Post extends Controller_Default
{

    public function index()
    {

        self::$template->header = new View_Base('/admin/shared/header.phtml');
        self::$template->header->pageTitle = 'Posts';
        self::$template->content = new View_Base('/admin/post/list.phtml');
        self::$template->content->posts = (new Model_Post())->loadCollection();
        self::$template->footer = new View_Base('/admin/shared/footer.phtml');

        return;
    }

    public function create()
    {
        self::$template->header = new View_Base('/admin/shared/header.phtml');
        self::$template->header->pageTitle = 'Create Post';
        self::$template->content = new View_Base('/admin/post/create.phtml');
        self::$template->content->templates = (new Model_Template())->loadCollection();
        self::$template->footer = new View_Base('/admin/shared/footer.phtml');

        return;
    }

    public function save()
    {
        $route = (new \Model_Route())->load(\Input::find('url_slug'), 'url');
        if ($route->getId() && $route->getStatus() == Model_Route::STATUS_ENABLED && $route->getHttpStatus() == 200) {
            $this->redirect('/admin/post/create?error=true');
            return;
        } else if ($route->getId() && $route->getStatus() == Model_Route::STATUS_DISABLED && $route->getHttpStatus() == 200) {
            $route->addData([
                'status' => Model_Route::STATUS_ENABLED,
            ])->save();
        } else if (!$route->getId() || $route->getHttpStatus() != 200) {
            $route = (new \Model_Route())->addData([
                'url' => \Input::find('url_slug'),
                'uuid' => \Flannel\Core\UUID::uuid4(),
                'http_status' => 200,
                'status' => Model_Route::STATUS_ENABLED,
            ])->save();
        } else {
            $this->redirect('/admin/post/create?error=true');
            return;
        }

        // Save form data
        $post = (new Model_Post())->addData([
            'uuid' => \Flannel\Core\UUID::uuid4(),
            'page_uuid' => $route->getUuid(),
            'title' => Input::find('title'),
            'html' => Input::find('html', FILTER_UNSAFE_RAW, FILTER_UNSAFE_RAW),
            'template_id' => Input::find('template'),
            'css' => Input::find('css', FILTER_UNSAFE_RAW, FILTER_UNSAFE_RAW),
            'meta' => Input::find('meta'),
            'status' => (int)Input::find('status'),
            'version' => 1,
        ])
            ->save();

        if (!$post->getId()) {
            $this->redirect('/admin/post/create?error=true');
            return;
        }

        $this->redirect('/admin/post/edit/id/' . $post->getUuid());

        return;
    }

    public function edit()
    {
        $postId = \Input::get('id');
        $post = (new \Model_Post())->load($postId, 'uuid');
        if (!$post->getId()) {
            $this->redirect('/admin/post');
            return;
        }

        $route = (new \Model_Route())->load($post->getPageUuid(), 'uuid');
        if (!$route->getId()) {
            $this->redirect('/admin/post');
            return;
        }

        self::$template->header = new View_Base('/admin/shared/header.phtml');
        self::$template->header->pageTitle = 'Edit Post';
        self::$template->content = new View_Base('/admin/post/edit.phtml');
        self::$template->content->post = $post;
        self::$template->content->route = $route;
        self::$template->content->templates = (new Model_Template())->loadCollection();
        self::$template->footer = new View_Base('/admin/shared/footer.phtml');

        return;
    }

    public function update()
    {
        $post = (new \Model_Post())->load(Input::find('post_id'), 'uuid');
        if (!$post->getId()) {
            $this->redirect('/admin/post/edit/id/' . Input::find('post_id') . '?error=true');
            return;
        }

        $route = (new \Model_Route())->load(\Input::find('url_slug'), 'url');
        if ($route->getId() && $route->getStatus() == Model_Route::STATUS_ENABLED && $route->getHttpStatus() == 200 && $route->getUuid() != $post->getPageUuid()) {
            $this->redirect('/admin/post/edit/id/' . Input::find('post_id') . '?error=true');
            return;
        } else if ($route->getId() && $route->getHttpStatus() == 200 && $route->getUuid() == $post->getPageUuid()) {
            $route->addData([
                'status' => Model_Route::STATUS_ENABLED,
            ])->save();
        } else if (!$route->getId() || $route->getHttpStatus() != 200) {
            $route = (new \Model_Route())->addData([
                'url' => \Input::find('url_slug'),
                'uuid' => \Flannel\Core\UUID::uuid4(),
                'http_status' => 200,
                'status' => Model_Route::STATUS_ENABLED,
            ])->save();
        } else {
            $this->redirect('/admin/post/edit/id/' . Input::find('post_id') . '?error=true');
            return;
        }

        // Save form data
        $newPost = (new Model_Post())->addData([
            'uuid' => \Flannel\Core\UUID::uuid4(),
            'page_uuid' => $route->getUuid(),
            'title' => Input::find('title'),
            'html' => Input::find('html', FILTER_UNSAFE_RAW, FILTER_UNSAFE_RAW),
            'template_id' => Input::find('template'),
            'css' => Input::find('css', FILTER_UNSAFE_RAW, FILTER_UNSAFE_RAW),
            'meta' => Input::find('meta'),
            'status' => (int)Input::find('status'),
            'version' => $post->getVersion() + 1,
        ])
            ->save();

        if (!$newPost->getId()) {
            $this->redirect('/admin/post/edit/id/' . Input::find('post_id') . '?error=true');
            return;
        }

        // Change the status of old post to updated
        $post->addData([
            'status' => Model_Post::STATUS_UPDATED,
        ])->save();

        $this->redirect('/admin/post/edit/id/' . $newPost->getUuid());

        return;
    }

}
