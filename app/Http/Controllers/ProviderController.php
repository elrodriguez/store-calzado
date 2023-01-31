<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProviderController extends Controller
{
    public function index()
    {
        $providers = (new Provider())->newQuery();
        if (request()->has('search')) {
            $providers->where('description', 'Like', '%' . request()->input('search') . '%');
        }
        if (request()->query('sort')) {
            $attribute = request()->query('sort');
            $sort_order = 'ASC';
            if (strncmp($attribute, '-', 1) === 0) {
                $sort_order = 'DESC';
                $attribute = substr($attribute, 1);
            }
            $providers->orderBy($attribute, $sort_order);
        } else {
            $providers->latest();
        }

        $providers = $providers->paginate(10)->onEachSide(2);

        return Inertia::render('Providers/List', [
            'providers' => $providers,
            'filters' => request()->all('search'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Providers/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'short_name' => 'required|unique:providers,short_name',
            'description' => 'required',
            'name' => 'required|unique:providers,name',
            'ruc' => 'required|numeric|unique:providers,ruc',
            'contact_name' => 'required',
            'contact_telephone' => 'required',
        ]);
        $path = 'img/imagen-no-disponible.jpeg';
        $destination = 'uploads/providers';
        $file = $request->file('image');
        if ($file) {
            $original_name = strtolower(trim($file->getClientOriginalName()));
            $file_name = time() . rand(100, 999) . $original_name;
            $path = $request->file('image')->storeAs(
                $destination,
                $file_name,
                'public'
            );
        }
        Provider::create([
            'short_name' => $request->get('short_name'),
            'name'  => $request->get('name'),
            'description'  => $request->get('description'),
            'image'  => $path,
            'ruc'  => $request->get('ruc'),
            'telephone'  => $request->get('telephone'),
            'email'  => $request->get('email'),
            'address'  => $request->get('address'),
            'contact_telephone'  => $request->get('contact_telephone'),
            'contact_name'  => $request->get('contact_name'),
            'contact_email'  => $request->get('contact_email'),
            'ubigeo'  => $request->get('ubigeo'),
        ]);

        return redirect()->route('products.create')
            ->with('message', __('Producto creado con éxito'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function show(Provider $provider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function edit(Provider $provider)
    {
        return Inertia::render('Providers/Edit', [
            'provider' => $provider
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Provider $provider)
    {
        // dd($request->all());
        $this->validate($request, [
            'short_name' => 'required|unique:providers,short_name'.$provider->id,
            'description' => 'required',
            'name' => 'required|unique:providers,name'.$provider->id,
            'ruc' => 'required|numeric'.$provider->id,
            'contact_name' => 'required',
            'contact_telephone' => 'required',
        ]);

        $path = 'img/imagen-no-disponible.jpeg';
        $destination = 'uploads/providers';
        $file = $request->file('image');

        if ($file) {
            $original_name = strtolower(trim($file->getClientOriginalName()));
            $file_name = time() . rand(100, 999) . $original_name;
            $path = $request->file('image')->storeAs(
                $destination,
                $file_name,
                'public'
            );
        }
        $total = 0;
        foreach ($request->get('sizes') as $k => $item) {
            $total = $total + $item['quantity'];
        }

        //dd($request->get('sale_prices'));
        $provider->update([
            'short_name' => $request->get('short_name'),
            'name'  => $request->get('name'),
            'description'  => $request->get('description'),
            'image'  => $path,
            'ruc'  => $request->get('ruc'),
            'telephone'  => $request->get('telephone'),
            'email'  => $request->get('email'),
            'address'  => $request->get('address'),
            'contact_telephone'  => $request->get('contact_telephone'),
            'contact_name'  => $request->get('contact_name'),
            'contact_email'  => $request->get('contact_email'),
            'ubigeo'  => $request->get('ubigeo'),
        ]);

        return redirect()->route('providers.edit', $provider->id)
            ->with('message', __('Proveedor editado con éxito'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function destroy(Provider $provider)
    {
        $provider->delete();
        return redirect()->route('providers.index')
            ->with('message', __('Providero eliminado con éxito'));
    }
}
