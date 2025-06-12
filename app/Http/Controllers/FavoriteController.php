<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;

use Stichoza\GoogleTranslate\GoogleTranslate;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->type === 'member') {
            $favorites = Favorite::with('product')->where('user_id', $user->id)->get();

            $tr = new GoogleTranslate('en');
            foreach ($favorites as $favorite) {
                $favorite->product->description_translated = $tr->translate($favorite->product->description);
            }

            return view('favorites.index', compact('favorites'));
        }
        return abort(403, 'This action is unauthorized.');
    }

    public function toggle($productId)
    {
        $user = auth()->user();

        if ($user->type === 'member') {
            $query = Favorite::where('user_id', $user->id)->where('product_id', $productId);

            if ($query->exists()) {
                $query->delete();
                return back()->with('alert-type', 'danger')->with('alert-msg', 'Produto removido dos favoritos!');
            } else {
                Favorite::create([
                    'user_id' => $user->id,
                    'product_id' => $productId,
                ]);
                return back()->with('alert-type', 'success')->with('alert-msg', 'Produto adicionado aos favoritos!');
            }
        }
        return abort(403, 'This action is unauthorized.');
    }
}
