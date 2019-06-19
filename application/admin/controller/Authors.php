<?php

namespace app\admin\controller;

use app\model\Author;
use think\App;
use think\Controller;
use think\Request;

class Authors extends BaseAdmin
{
    protected $authorService;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->authorService = new \app\service\AuthorService();
    }

    public function index()
    {
        $authorService = new \app\service\AuthorService();
        $data = $this->authorService->getAuthors();
        $this->assign([
            'authors' => $data['authors'],
            'count' => $data['count']
        ]);
        return view();
    }

    public function getBooksByAuthor($author_name){
        $data = $this->authorService->getBooksByAuthor($author_name); //查出书籍
        $this->assign([
            'books' => $data['books'],
            'count' => count($data['books'])
        ]);
        return view('books/index');
    }

    public function search($author_name){
        $data = $this->authorService->getAuthors([
            ['author_name','like','%'.$author_name.'%']
        ]);
        $this->assign([
            'authors' => $data['authors'],
            'count' => $data['count']
        ]);
        return view('index');
    }

    public function create(){
        return view();
    }

    public function save(){
        $author_name = input('author_name');
        $author = new Author();
        $author->author_name = $author_name;
        $author->save();
        $this->success('作者新增成功');
    }

    public function edit($id)
    {
        $author = Author::get($id);
        $this->assign([
            'author' => $author,
        ]);
        return view();
    }

    public function update(Request $request)
    {
        $data = $request->param();
        $result = Author::update($data);
        if ($result){
            $this->success('编辑成功');
        }else{
            $this->error('编辑失败');
        }
    }

    public function delete($id)
    {
        $author = Author::get($id);
        $books = $author->books;
        if (count($books) > 0){
            return ['err' => '1','msg' => '该作者名下还有作品，请先删除所有作品'];
        }
        $author->delete();
        return ['err' => '0','msg' => '删除成功'];
    }
}
