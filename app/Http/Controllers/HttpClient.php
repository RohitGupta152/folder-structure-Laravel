<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class HttpClient extends Controller
{
    public function testClient()
    {
        $response = Http::get('https://jsonplaceholder.typicode.com/posts/1');
        $data = $response->json();
        dump($data);


        $response = Http::post('https://jsonplaceholder.typicode.com/posts', [
            'title' => 'foo',
            'body' => 'bar',
            'userId' => 1,
        ]);
        dump($response);


        Http::put('https://jsonplaceholder.typicode.com/posts', [
            'title' => 'Updated Title',
        ]);


        Http::delete('https://jsonplaceholder.typicode.com/posts');


        Http::withHeaders([
            'Authorization' => 'Bearer your-token',
        ])->get('https://jsonplaceholder.typicode.com/posts');


        Http::withToken('your-token')->get('https://jsonplaceholder.typicode.com/posts');


        Http::get('https://jsonplaceholder.typicode.com/posts', [
            'title' => 'sunt',
            'limit' => 5,
        ]);


        Http::timeout(5)->retry(3, 100)->get('https://api.example.com/slow');


        $promise = Http::async()->get('https://api.example.com/data');
        $response = $promise->wait();


        Http::withCookies([
            'name' => 'value',
        ], 'example.com')->get('https://example.com');


        Http::acceptJson()->get('https://api.example.com/data');


        Http::get('https://example.com/image.jpg')
            ->save(storage_path('app/public/image.jpg'));


        $response = Http::get('https://api.example.com/status');
        if ($response->successful()) {
            return "success";
        } elseif ($response->failed()) {
            return "error";
        }
        $response = Http::get('https://api.example.com/data')->throw();


        Http::macro('github', function () {
            return Http::withHeaders([
                'Authorization' => 'Bearer github-token',
            ]);
        });
        $response = Http::github()->get('https://api.github.com/user');


        $response = Http::post('https://api.example.com/data', [
            'name' => 'John',
        ]);
        $data = $response->json();
    }
}
