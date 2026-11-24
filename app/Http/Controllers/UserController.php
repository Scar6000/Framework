<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Controllers\Concerns\RendersView;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Filiere;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** Gestion des comptes (admin uniquement). Aucune inscription publique. */
class UserController extends Controller
{
    use RendersView;

    public function index(Request $request)
    {
        $users = User::query()->with('filiere')
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->input('role')))
            ->when($request->filled('filiere_id'), fn ($q) => $q->where('filiere_id', $request->input('filiere_id')))
            ->when($request->filled('actif'), fn ($q) => $q->where('is_active', $request->boolean('actif')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $t = '%'.$request->input('q').'%';
                $q->where(fn ($w) => $w->where('name', 'like', $t)->orWhere('prenoms', 'like', $t)->orWhere('email', 'like', $t));
            })
            ->orderBy('name')->paginate(15)->withQueryString();

        return $this->render('admin.users.index', [
            'users' => $users,
            'roles' => Role::cases(),
            'filieres' => Filiere::orderBy('code')->get(),
        ]);
    }

    public function create()
    {
        return $this->render('admin.users.form', ['user' => null, 'roles' => Role::cases(), 'filieres' => Filiere::orderBy('code')->get()]);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $temporaire = $this->motDePasseTemporaire();

        $user = User::create([
            'name' => $data['name'],
            'prenoms' => $data['prenoms'],
            'email' => $data['email'],
            'role' => $data['role'],
            'filiere_id' => $data['role'] === Role::Chef->value ? $data['filiere_id'] : null,
            'password' => $temporaire,
            'is_active' => true,
            'must_change_password' => true,
        ]);

        // Le mot de passe temporaire n'est affiché qu'UNE fois (flash) : à transmettre à l'utilisateur.
        return $this->done(
            'admin.users.show', ['user' => $user],
            'Compte créé. Transmettez le mot de passe temporaire à l\'utilisateur.',
            ['user' => $user, 'mot_de_passe_temporaire' => $temporaire], 201,
            ['mot_de_passe_temporaire' => $temporaire]
        );
    }

    public function show(User $user)
    {
        return $this->render('admin.users.show', ['user' => $user->load('filiere')]);
    }

    public function edit(User $user)
    {
        return $this->render('admin.users.form', ['user' => $user, 'roles' => Role::cases(), 'filieres' => Filiere::orderBy('code')->get()]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $user->update([
            'name' => $data['name'],
            'prenoms' => $data['prenoms'],
            'email' => $data['email'],
            'role' => $data['role'],
            'filiere_id' => $data['role'] === Role::Chef->value ? $data['filiere_id'] : null,
        ]);

        return $this->done('admin.users.show', ['user' => $user], 'Compte mis à jour.', ['user' => $user->fresh()]);
    }

    /** Active / désactive un compte (on ne supprime jamais : l'historique est conservé). */
    public function activation(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            $this->refuser('Vous ne pouvez pas désactiver votre propre compte.');
        }

        $nouvelEtat = ! $user->is_active;

        if (! $nouvelEtat && $user->isAdmin()) {
            $autres = User::where('role', Role::Admin->value)->where('is_active', true)->where('id', '!=', $user->id)->exists();
            if (! $autres) {
                $this->refuser('Impossible : c\'est le dernier administrateur actif.');
            }
        }

        if ($nouvelEtat && $user->isChef()) {
            $autreChef = User::where('role', Role::Chef->value)->where('filiere_id', $user->filiere_id)
                ->where('is_active', true)->where('id', '!=', $user->id)->exists();
            if ($autreChef) {
                $this->refuser('Cette filière a déjà un chef de département actif.');
            }
        }

        $user->update(['is_active' => $nouvelEtat]);

        return $this->done('admin.users.show', ['user' => $user], $nouvelEtat ? 'Compte activé.' : 'Compte désactivé.', ['user' => $user->fresh()]);
    }

    public function reinitialiserMotDePasse(User $user)
    {
        $temporaire = $this->motDePasseTemporaire();
        $user->forceFill(['password' => $temporaire, 'must_change_password' => true])->save();
        ActivityLogger::log('reinitialisation_mot_de_passe', $user);

        return $this->done(
            'admin.users.show', ['user' => $user], 'Mot de passe réinitialisé.',
            ['mot_de_passe_temporaire' => $temporaire], 200,
            ['mot_de_passe_temporaire' => $temporaire]
        );
    }

    private function motDePasseTemporaire(): string
    {
        return Str::password(config('pv.password_temp_length', 10), symbols: false, spaces: false);
    }

    private function refuser(string $message): never
    {
        throw ValidationException::withMessages(['user' => $message]);
    }
}
