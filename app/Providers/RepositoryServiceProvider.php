<?php

namespace App\Providers;

use App\Repositories\Contracts\AutorRepositoryInterface;
use App\Repositories\Contracts\LibroRepositoryInterface;
use App\Repositories\Contracts\PrestamoRepositoryInterface;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\Eloquent\AutorRepository;
use App\Repositories\Eloquent\LibroRepository;
use App\Repositories\Eloquent\PrestamoRepository;
use App\Repositories\Eloquent\UsuarioRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        AutorRepositoryInterface::class    => AutorRepository::class,
        LibroRepositoryInterface::class    => LibroRepository::class,
        PrestamoRepositoryInterface::class => PrestamoRepository::class,
        UsuarioRepositoryInterface::class  => UsuarioRepository::class,
    ];

    public function register()
    {
        //
    }

    public function boot()
    {
        //
    }
}
