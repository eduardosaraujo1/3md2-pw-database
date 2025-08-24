# Sistema de Cadastro de Contato

Este projeto é um sistema de cadastro de contato com validação de dados em tempo real no front-end e verificação de unicidade no back-end, utilizando HTML, jQuery, PHP (com PDO) e MySQL.

## Como Executar

1. Clone o projeto na pasta `/xampp/htdocs`
    - Importante: não coloque o projeto dentro de uma subpasta (como C:\xampp\htdocs\3md2-pw-database), pois isso quebra o sistema de URL.
2. Opcionalmente, troque o banco de dados de `sqlite` para `mysql` pelo arquivo [config/database.php](./config/database.php)
3. Inicie o servidor

## Framework

A estrutura da aplicação é um hibrido entre os padrões vistos em [PHP Framework PRO - Gary Clarke](https://www.youtube.com/watch?v=5FxuPuJkCGs&list=PLQH1-k79HB3-0SKspp8814ZI1GIqRYLAu), a estrutura do framework Laravel, e algumas simplificações.

### Views e Frontend JQuery

O frontend (HTML, CSS, Javascript e JQuery) está localizado na pasta `resources`. Coloque seus arquivos HTML dentro de `resources/views`, seu CSS dentro de `resources/css`, e o Javascript dentro de `resources/js`.

Será possível apontar para qual arquivo HTML deve ser exibido utilizando a linha de código `response()->view('arquivo.html')`, assumindo que o nome do seu HTML é arquivo.html e ele está localizado em `resources/views/arquivo.html`.

### Classes Request e Response

#### Request

A classe Request é utilizada para substituir as variáveis globais do PHP (\$\_GET, \$\_POST, \$\_FILES e \$\_SERVER). Ela possui métodos para ler os dados do form enviado pelo frontend, e dados de rota.

```php
use Core\Http\Request;

// Ao colocar o código abaixo em `routes/web.php`, tente iniciar o Apache e abrir
$router->post('/testar/request', function() {
    // Criar objeto Request usando Service Container (recomendado)
    $request = app()->make(Request::class);
    // Alternativamente, é possível criar o Request utilizando o método FromGlobals
    $requestNova = Request::createFromGlobals();

    // Exemplo do método all()
    // Retorna todos os parâmetros enviados pelo form, seja GET, POST ou um arquivo
    $todosParametros = $request->all();

    // Exemplo do método only()
    // Retorna apenas os parâmetros 'name' e 'email'
    $parametrosEspecificos = $request->only(['name', 'email']);

    // Exemplo do método method()
    $metodoHttp = $request->method();
    // Retorna o método HTTP da requisição, como 'GET' ou 'POST'

    // Exemplo do método uri()
    $uriRequisicao = $request->uri();
    // Retorna a URI da requisição, como '/users'

    return [
        'all' => $todosParametros,
        'only' => $parametrosEspecificos,
        'method' => $metodoHttp,
        'uri' => $uriRequisicao
    ];
});
```

#### Response

Ao programar nosso app em PHP, podemos precisar das seguintes funcionalidades:

-   **Retornar Views**: Ao acessar `http://localhost/home`, mostrar o HTML que está em `resources/views/home.html`
-   **Retornar JSON**: Ao fazer um `<form>` com `action="/usuarios/criar"`, retornar uma resposta em JSON como `{"message": "Usuário criado com sucesso"}`
-   **Redirect**: Ao acessar `http://localhost/sair`, encerrar sessão o usuário e mandar ele para URL `http://localhost/login`, definindo o status HTTP 301.
-   **Retornar Erro em JSON**: Ao acessar `http://localhost/usuarios/criar` com um e-mail duplicado, retornar JSON falando `{"errors": ["Esse nome já está no banco de dados"]}` e definir o status HTTP 400.

Para cobrir essas necessidades, existe uma função chamada `response()`, que cria objetos da classe `Response` conforme os exemplos:

```php
$router->get('/testar/response', function () {
    // Retornar texto direto
    $plainResponse = response(
        body: "<h1>HTML Direto</h1>",
        status: 200 // Esse número é o código de resposta HTTP, outro exemplo é o 404 que significa "Not Found" - 200 significa "sucesso"
    )
    // Retornar uma View
    // Leia a documentação de "Frontend" para aprender onde colocar 'home.html'
    $viewResponse = response()->view('home.html');

    // Retornar JSON
    $jsonResponse = response()->json(['message' => 'Usuário criado com sucesso'], 201); // 201 significa "objeto criado"

    // Redirecionar
    $redirectResponse = response()->redirect('/login');

    // Retornar erro com status diferente
    $errorResponse = response()->json(['errors' => ['Esse nome já está no banco de dados']], 400);

    // Definir header de resposta (avançado)
    $customHeaderResponse = response('Custom Header Response')
        ->header('X-Custom-Header', 'Valor do Header');

    return [
        'plain' => var_dump($plainResponse),
        'view' => var_dump($viewResponse),
        'json' => var_dump($jsonResponse),
        'redirect' => var_dump($redirectResponse),
        'error' => var_dump($errorResponse),
        'customHeader' => var_dump($customHeaderResponse)
    ];

});
```

### Rotas

As rotas do sistema definem como URLs são mapeadas para controladores e métodos responsáveis por processar as requisições. Todas as rotas web são registradas no arquivo `routes/web.php`.

#### Como definir uma rota

Cada rota associa um método HTTP (GET ou POST), um caminho (URL) e uma ação (método):

```php
$router->get('/login', function (): Response {
    // Equivalente a response('<p>Tela de login</p>');
    return `<p>Tela de login</p>`;
});
// Por padrão, rotas recebem uma $request como parâmetro, pra evitar ficar usando app()->make()
$router->post('/signin', function (Request $request): Response {
    // Equivalente a response()->json(['mensagem': 'Usuário autenticado com sucesso'])
    return [
        'mensagem': 'Usuário autenticado com sucesso'
    ];
});
$router->post('/signout', function (Request $request): Response {
    return response()->redirect('/login');
});
```

#### Retorno de uma rota

Uma rota pode retornar um array, uma string, ou um objeto _**Response**_ criado utilizando o método `response()` (leia a documentação de Request e Response para entender).

Se o retorno for uma string, o framework vai criar um objeto Response presumindo que a string é um HTML. Se o retorno for um array, o PHP vai criar um objeto Response que retorna JSON

### Service Container

A função `app()->make()` é um gerador de objetos que utiliza o Service Container para criar e gerenciar dependências. De forma simples, é um objeto que guarda outros objetos da forma correta, seja com o padrão factory ou singleton.

Por exemplo, a classe Database faz conexão com o banco de dados quando ela é criada. Se a gente utilizasse `new Database()` sempre que precisasse dessa conexão, ela seria realizada mais de uma vez, o que não é ideal. O Service Container vai criar o objeto dessa classe e guardar num array, então quando eu utilizar `app()->make(Database::class)`, o objeto dentro do array vai ser retornado ao em vez de um novo ser criado. Não importa quantas vezes `make` é chamado ou onde ele é executado, o Database só é criado uma vez (esse padrão se chama singleton).

Mas existe um problema, como um objeto Database é criado? Na versão de agosto de 2025 do framework, ele precisa ser criado assim:

```php
new Database(new MySQLConnection(host: "localhost", user: "root", password: "root", port: 3306, database: "pw_database", migrateFile: "database/migrations/mysql.sql"))
```

Para criar um `Database`, precisa de um `MySQLConnection` (ou `SQLiteConnection`), e para criar um `MySQLConnection`, precisa das credenciais do banco de dados. Como especificar pro Service Container onde achar essas credenciais e criar os objetos da forma certa?

Os Providers resolvem esse problema. Eles são classes localizadas em `app/Providers`, e precisam ser registradas em `config/providers.php`. Com elas, o usuário pode escrever uma função que cria um desses objetos, e essa função vai ser executada quando o objeto precisar ser criado, seja uma ou várias vezes.

#### Criando um Provider

O framework já vem com os providers de suas classes padrões (como Database), mas você pode registrar suas próprias classes no arquivo `app\Providers\AppServiceProvider.php`.

-   Exemplo de um Provider:

```php
namespace App\Providers;

use Core\Container\Container;
use Core\Providers\Provider;
use Core\Support\Connection\Connection;
use Core\Services\Database;

class AppServiceProvider extends Provider
{
    public function register()
    {
        // $this->app->bind() executará a função novamente sempre que app()->make() for executado
        // $this->app->singleton() só executa a função uma vez e retorna o objeto final sempre que necessário
        $this->app->singleton(Connection::class, function(Container $container) {
        // A função config lê as credenciais de conexão do MySQL do arquivo `config/database.php`
        $config_banco = config("database");
        if ($config_banco["default"] === "mysql") {
            return new MySQLConnection(
                
            );
        }
    });
        $this->app->singleton(Database::class, function (Container $container) {
            $connection = $container->make(Connection::class);
            return new Database($connection);
    }

    public function boot()
    {
        //
    }
}
```

#### Registrando provider em outro arquivo

É possível seguir o exemplo acima e editar diretamente o arquivo `app/Providers/AppServiceProvider.php`. Alternativamente, você pode criar um novo arquivo para atuar como Provider, adicionar seus binds nele e registrá-lo no arquivo `config/providers.php`.

```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\CustomServiceProvider::class
];
```

#### Utilizando Provider

Após registrar o Provider, você pode usar o Service Container `app()` para criar instâncias de serviços. Exemplo de uso em uma rota:

```php
$router->post('/users', function () {
    $database = app()->make(Database::class);
    return $database->fetch('SELECT * FROM users');
});
```

#### Injeção de dependência

O Service Container também permite injetar dependências. Por exemplo, ao registrar um Controller (veja a frente) no AppServiceProvider:

```php
$this->app->singleton(UserController::class, function (Container $container) {
    $userService = $container->make(UserService::class);
    return new UserController(userService: $userService);
});
```

Agora, ao usar `app()->make(UserController::class)`, o Service Container cria um Controller que já possui um objeto como dependência

### Controllers

Um Controller encapsula a lógica feita em cada rota em um método, permitindo uma melhor organização e reutilização do código. Ele atua como intermediário entre as requisições do usuário e os serviços ou modelos que processam os dados.

#### Definição de um controller

-   Para criar um controller, crie um arquivo em `app/Controllers`
-   Exemplo de código para Controller:

```php
namespace App\Controllers;

use Core\Http\Request;
use Core\Http\Response;

class UserController
{
    public function showProfile(Request $request): Response
    {
        $userId = $request->get('id');
        // Lógica para buscar o perfil do usuário
        return response()->json(['id' => $userId, 'name' => 'John Doe']);
    }
}
```

-   Exemplo de rota que usa Controller:

```php
$router->get('/users', function() {
    $userController = app()->make(UserController::class); // Leia o próximo tópico sobre app()->make()

    return $userController->listar(); // Retorna um array de todos os usuários
});
```

### Serviços

O aplicativo introduz três serviços principais: `Database`, `Storage` e `Session`. Esses serviços são projetados para facilitar a interação com o banco de dados, o armazenamento de arquivos e o \$\_SESSION do PHP, respectivamente.

#### Database

A classe `Database` é responsável por gerenciar a conexão com o banco de dados e executar consultas SQL. Ela utiliza a classe `Connection` para obter uma instância de PDO. Exemplos de uso:

```php
use Core\Services\Database;

$router->post('/testar/database', function() {
    $database = app()->make(Database::class);

    // Executar uma consulta segura
    $database->query('INSERT INTO users (name, email) VALUES (?, ?)', ['John Doe', 'john@example.com']);

    // Buscar dados
    $users = $database->fetch('SELECT * FROM users WHERE active = ?', [1]);

    return $users;
});
```

#### Storage

A classe `Storage` gerencia o armazenamento de arquivos no diretório configurado (por padrão, a pasta `storage`. Suporte para alterar a pasta que se armazena os arquivos será adicionado futuramente). Ela permite salvar arquivos enviados pelo usuário e gera nomes únicos para evitar conflitos. Exemplos de uso:

```php
use Core\Services\Storage;

$router->post('/arquivo/guardar', function() {
    $storage = app()->make(Storage::class);
    $request -> app()->make(Request::class);

    // Salvar um arquivo enviado
    $arquivo = $request->only(['file']);
    $nomeOriginal = $arquivo['name'];
    $caminhoOriginal = $arquivo['tmp_name'];
    $filePath = $storage->storeFile($caminhoOriginal, $nomeOriginal);

    // Caminho relativo do arquivo salvo
    return $filePath;
});
```

#### Session

A classe `Session` fornece métodos para gerenciar dados de sessão, como armazenar, recuperar e remover valores. Ela também suporta valores "flash", que são válidos apenas para a próxima requisição. Exemplos de uso:

```php
use Core\Services\Session;

$router->get('/testar/session', function() {

    $session = app()->make(Session::class);

    // Armazenar um valor na sessão
    $session->put('user_id', 123);

    // Recuperar um valor da sessão
    $userId = $session->get('user_id');

    // Remover um valor da sessão
    $session->remove('user_id');

    // Usar valores flash
    $session->flash('message', 'Bem-vindo!');
    return $session->getFlash('message');
});
```

### Banco de Dados

A configuração do banco de dados é feita no arquivo `config/database.php`. O aplicativo suporta os bancos de dados MySQL e SQLite. Caso o banco de dados ainda não tenha sido criado, ele será gerado automaticamente.

#### Migrações

Os scripts de criação do banco de dados, por padrão, estão localizados na pasta `database/migrations`. **É importante notar que não é necessário incluir o CREATE DATABASE no script do MySQL, pois isso é feito automaticamente pelo PHP**. A localização dos scripts é configurada no arquivo `config/database.php`. Exemplos de scripts:

-   **MySQL**:

```sql
CREATE TABLE IF NOT EXISTS tb_contato(
    id int primary key auto_increment,
    nome VARCHAR(255) NOT NULL,
    login VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefone VARCHAR(255) NOT NULL,
    foto VARCHAR(255)
);

INSERT INTO tb_contato VALUES
    (NULL, "Admin", "admin", "$2a$12$IYe6qvlevtzmCxu4zjkIIuLmrPMIvBwmhl3YApHE7fuxI9cadkesW", 'admin@gmail.com', '11951490211', NULL); -- Senha: admin
```

-   **SQLite**:

```sql
CREATE TABLE IF NOT EXISTS tb_contato(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    login TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    telefone TEXT NOT NULL,
    foto TEXT
);

INSERT INTO tb_contato VALUES
    (NULL, "Admin", "admin", "$2a$12$IYe6qvlevtzmCxu4zjkIIuLmrPMIvBwmhl3YApHE7fuxI9cadkesW", 'admin@gmail.com', '11951490211', NULL); -- Senha: admin
```

# Roadmap

-   [ ] Adicionar suporte para outros verbos HTTP (PUT/PATCH e DELETE)
-   [ ] Adicionar suporte para parâmetros de rotas (`/users/{id}`)
-   [ ] Criar diretório /public e endpoint /user/{id}/foto para melhorar segurança

# Testes
