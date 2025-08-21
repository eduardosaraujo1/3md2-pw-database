<?php

use App\Services\UserService;
use App\Domain\DTO\UserDTO;
use Mockery as m;
use App\Repositories\UserRepository;
use Core\Services\Session;
use App\Services\ImageStorageService;
use App\Exceptions\UserException;
use App\Exceptions\QueryException;

beforeEach(function () {
    $this->userRepository = m::mock(UserRepository::class)->makePartial();
    $this->sessionService = m::mock(Session::class)->makePartial();
    $this->imageStorageService = m::mock(ImageStorageService::class)->makePartial();

    $this->userService = new UserService(
        $this->userRepository,
        $this->sessionService,
        $this->imageStorageService
    );
});

afterEach(function () {
    m::close();
});

test('createUser stores user and returns the created user', function () {
    $userDTO = new UserDTO(
        id: null,
        nome: 'John Doe',
        login: 'johndoe',
        email: 'john@example.com',
        senha: 'password123',
        telefone: '123456789',
        foto: ['image_data']
    );

    $this->imageStorageService
        ->shouldReceive('store')
        ->once()
        ->with(['image_data'])
        ->andReturn('stored_image_path');

    $this->userRepository
        ->shouldReceive('insert')
        ->once()
        ->with(m::on(function ($data) {
            return $data['foto'] === 'stored_image_path';
        }));

    $this->userRepository
        ->shouldReceive('getLatest')
        ->once()
        ->andReturn((object) ['id' => 1, 'nome' => 'John Doe']);

    $createdUser = $this->userService->createUser($userDTO);

    expect($createdUser)->toBeObject();
    expect($createdUser->id)->toBe(1);
    expect($createdUser->nome)->toBe('John Doe');
});

test('createUser throws QueryException on database error', function () {
    $userDTO = new UserDTO(
        id: null,
        nome: 'John Doe',
        login: 'johndoe',
        email: 'john@example.com',
        senha: 'password123',
        telefone: '123456789',
        foto: null
    );

    $this->userRepository
        ->shouldReceive('insert')
        ->once()
        ->andThrow(QueryException::class);

    $this->userService->createUser($userDTO);
})->throws(QueryException::class);

test('updateUser updates user and returns the updated user', function () {
    $userDTO = new UserDTO(
        id: 1,
        nome: 'John Doe Updated',
        login: 'johndoe',
        email: 'john_updated@example.com',
        senha: 'newpassword123',
        telefone: '987654321',
        foto: null
    );

    $this->userRepository
        ->shouldReceive('findById')
        ->once()
        ->with('1')
        ->andReturn((object) ['id' => 1, 'nome' => 'John Doe']);

    $this->userRepository
        ->shouldReceive('update')
        ->once()
        ->with($userDTO)
        ->andReturn(true);

    $this->userRepository
        ->shouldReceive('findById')
        ->once()
        ->with('1')
        ->andReturn((object) ['id' => 1, 'nome' => 'John Doe Updated']);

    $updatedUser = $this->userService->updateUser($userDTO);

    expect($updatedUser)->toBeObject();
    expect($updatedUser->id)->toBe(1);
    expect($updatedUser->nome)->toBe('John Doe Updated');
});

test('updateUser throws UserException if user not found', function () {
    $userDTO = new UserDTO(
        id: 1,
        nome: 'John Doe Updated',
        login: 'johndoe',
        email: 'john_updated@example.com',
        senha: 'newpassword123',
        telefone: '987654321',
        foto: null
    );

    $this->userRepository
        ->shouldReceive('findById')
        ->once()
        ->with('1')
        ->andReturn(null);

    $this->userService->updateUser($userDTO);
})->throws(UserException::class, 'Usuário não encontrado.');
