<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        $clients = (new Person())->newQuery()->where('is_client', true)->where('number', '<>', 99999999);
        if (request()->has('search')) {
            $clients->where('full_name', 'Like', '%' . request()->input('search') . '%');
        }
        if (request()->query('sort')) {
            $attribute = request()->query('sort');
            $sort_order = 'ASC';
            if (strncmp($attribute, '-', 1) === 0) {
                $sort_order = 'DESC';
                $attribute = substr($attribute, 1);
            }
            $clients->orderBy($attribute, $sort_order);
        } else {
            $clients->latest();
        }

        $clients = $clients->paginate(10)->onEachSide(2);

        return Inertia::render('Clients/List', [
            'clients' => $clients,
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
        return Inertia::render('Clients/Create');
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
            'document_type_id' => 'required',
            'full_name' => 'required|unique:people,full_name',
            'number' => 'required|numeric|unique:people,number',
            'email' => 'email|unique:people,email',
        ]);
        
        if(count(Person::where('number', $request->get('number'))->get())==0){
            Person::create([
                'full_name'  => $request->get('full_name'),
                'document_type_id' => $request->get('document_type_id'),
                'number'  => $request->get('number'),
                'telephone'  => $request->get('telephone'),
                'email'  => $request->get('email'),
                'address'  => $request->get('address'),
                'is_client' => true
            ]);
        }else{
            Person::where('number', $request->get('number'))->first()->update([
                'full_name'  => $request->get('full_name'),
                'document_type_id' => $request->get('document_type_id'),
                'number'  => $request->get('number'),
                'telephone'  => $request->get('telephone'),
                'email'  => $request->get('email'),
                'address'  => $request->get('address'),
                'is_client' => true
            ]);
        }

        return redirect()->route('clients.create')
            ->with('message', __('Cliente registrado con éxito'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Person  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Person $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Person  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Person $client)
    {
        return Inertia::render('Clients/Edit', [
            'client' => $client
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Person  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Person $client)
    {
        // dd($request->all());
        $this->validate($request, [
            'full_name' => 'required',
            'number' => 'required|numeric|unique:people,number,'.$client->id,
            'document_type_id' => 'required',
            'email' => 'required|unique:people,email,'.$client->id
        ]);

        //dd($request->get('sale_prices'));
       

        $client->update([
            'full_name'  => $request->get('full_name'),
            'number'  => $request->get('number'),
            'telephone'  => $request->get('telephone'),
            'document_type_id'  => $request->get('document_type_id'),
            'address'  => $request->get('address'),
            'email' => $request->get('email'),
        ]);

        return redirect()->route('clients.edit', $client->id)
            ->with('message', __('Cliente editado con éxito'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Person  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Person $client)
    {
        $client->delete();
        return redirect()->route('clients.index')
            ->with('message', __('Cliento eliminado con éxito'));
    }
}
