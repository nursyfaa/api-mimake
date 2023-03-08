<?php

namespace App\Http\Controllers;

use App\Models\BarangM;
use Illuminate\Http\Request;
use App\Http\Resources\BarangR;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BarangC extends Controller
{
    public function index()
    {
        $barang = BarangM::latest()->paginate(5);

        return new BarangR(true, 'List Data Barang', $barang);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required',
            'gambar_barang' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webm',
            'qty' => 'required',
            'harga' => 'required',
            'barcode' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $gambar_barang = $request->file('gambar_barang');
        $gambar_barang->storeAs('public/barang', $gambar_barang->hashName());

        $barang = BarangM::create([
            'nama_barang' => $request->nama_barang,
            'gambar_barang' => $gambar_barang->hashName(),
            'qty' => $request->qty,
            'harga' => $request->harga,
            'barcode' => $request->barcode,
        ]);

        return new BarangR(true,'Data Barang Berhasil Di Tambahkan!', $barang);
    }

    public function show(BarangM $barang){
        return new BarangR(true, 'Data Barang Di Temukan!', $barang);
    }
    public function update(Request $request, BarangM $barang){
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required',
            'qty' => 'required',
            'harga' => 'required',
            'barcode' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        if($request ->hasfile('gambar_barang')){
            $gambar_barang = $request->file('gambar_barang');
            $gambar_barang->storeAs('public/barang', $gambar_barang->hashName());

            Storage::delete('public/barang/'.$barang->image);

            $barang->update([
                'nama_barang' => $request->nama_barang,
                'gambar_barang' => $gambar_barang->hashName(),
                'qty' => $request->qty,
                'harga' => $request->harga,
                'barcode' => $request->barcode,
            ]);
        }else{
            $barang->update([
                'nama_barang' => $request->nama_barang,
                'qty' => $request->qty,
                'harga' => $request->harga,
                'barcode' => $request->barcode,
            ]);
        }
        return new BarangR(true, 'Data Barang Berhasil Diubah!', $barang);
    }

    public function destroy(BarangM $barang){
        Storage::delete('public/barang'.$barang->gambar_barang);

        $barang->delete();

        return new BarangR(true, 'Data Barang Berhasil Dihapus!', null);
    }
}
