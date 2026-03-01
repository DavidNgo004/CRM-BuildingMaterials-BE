<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(){
        return response() -> json([
            'action' => 'Danh Sách Vật Liệu Xây Dựng'
        ]);
    }

    public function store(Request $request){
        return response() -> json([
            'action' => 'Thêm Vật Liệu Xây Dựng'
        ]);
    }

     public function show($id){
        return response() -> json([
            'action' => 'Chi Tiết Vật Liệu Xây Dựng'
        ]);
    }

     public function update(Request $request, $id){
        return response() -> json([
            'action' => 'Cập Nhật Vật Liệu Xây Dựng'
        ]);
    }

     public function destroy($id){
        return response() -> json([
            'action' => 'Xóa Vật Liệu Xây Dựng'
        ]);
    }
}

?>
