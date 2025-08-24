# Documentação do Framework

Este documento tem como objetivo guiar você no uso do framework, mesmo que você não tenha experiência prévia com frameworks como Laravel ou conceitos avançados de programação orientada a objetos (POO). Vamos começar do básico e construir o conhecimento necessário para que você possa usar o framework com confiança.

## Estrutura do Projeto

O framework segue uma estrutura organizada para facilitar o desenvolvimento. Aqui estão as principais pastas e seus propósitos:

-   **`app/`**: Contém a lógica principal da aplicação, como controladores, serviços e modelos.
-   **`config/`**: Arquivos de configuração, como o banco de dados.
-   **`core/`**: Contém o núcleo do framework, como classes para requisições, respostas e roteamento.
-   **`resources/`**: Arquivos relacionados ao frontend, como HTML, CSS e JavaScript.
-   **`routes/`**: Define as rotas da aplicação.
-   **`storage/`**: Armazena arquivos enviados pelo usuário.
-   **`database/`**: Scripts de criação do banco de dados e o próprio banco SQLite, se usado.

---

## Como Funciona o Framework

O framework é baseado em rotas, controladores e serviços. Ele permite que você defina como as URLs da sua aplicação se conectam à lógica do backend.

### 1. Rotas

As rotas definem como as URLs são mapeadas para ações específicas. Todas as rotas são registradas no arquivo `routes/web.php`.

#### Exemplo de Rota

```php
$router->get('/home', function () {
    return response()->view('home.html');
});
```

Neste exemplo, ao acessar `http://localhost/home`, o framework exibirá o arquivo `resources/views/home.html`.

#### Métodos Suportados

-   **GET**: Para buscar informações.
-   **POST**: Para enviar dados ao servidor.

#### Retornos Possíveis

-   **String**: Retorna HTML diretamente.
-   **Array**: Retorna JSON automaticamente.
-   **Objeto Response**: Permite personalizar a resposta (veja a seção de Respostas).

---

### 2. Requisições (Request)

A classe `Request` substitui as variáveis globais do PHP, como `$_GET` e `$_POST`. Ela facilita o acesso aos dados enviados pelo frontend.

#### Exemplo de Uso

```php
use Core\Http\Request;

$router->post('/form', function () {
    $request = app()->make(Request::class);

    $dados = $request->all(); // Retorna todos os dados enviados
    $nome = $request->only(['nome']); // Retorna apenas o campo 'nome'

    return response()->json($dados);
});
```

---

### 3. Respostas (Response)

A classe `Response` permite retornar diferentes tipos de respostas ao cliente.

#### Exemplos de Respostas

-   **Texto Simples**:

```php
return response('Olá, mundo!', 200);
```

-   **HTML**:

```php
return response()->view('home.html');
```

-   **JSON**:

```php
return response()->json(['mensagem' => 'Sucesso!'], 201);
```

-   **Redirecionamento**:

```php
return response()->redirect('/login');
```

---

### 4. Controladores (Controllers)

Os controladores organizam a lógica da aplicação. Eles são classes localizadas em `app/Controllers`.

#### Exemplo de Controlador

```php
namespace App\Controllers;

use Core\Http\Request;
use Core\Http\Response;

class UserController
{
    public function showProfile(Request $request): Response
    {
        $userId = $request->get('id');
        return response()->json(['id' => $userId, 'name' => 'John Doe']);
    }
}
```

#### Usando um Controlador em uma Rota

```php
$router->get('/user', function () {
    $controller = app()->make(UserController::class);
    return $controller->showProfile();
});
```

---

### 5. Serviços

Os serviços são classes que encapsulam funcionalidades específicas, como acesso ao banco de dados ou gerenciamento de arquivos.

#### Serviço Banco de Dados

A classe `Database` gerencia a conexão com o banco de dados e permite executar consultas SQL.

```php
use Core\Services\Database;

$router->post('/users', function () {
    $db = app()->make(Database::class);
    $db->query('INSERT INTO users (name) VALUES (?)', ['John Doe']);
    return response()->json(['mensagem' => 'Usuário criado!']);
});
```

#### Serviço Armazenamento de Arquivos

A classe `Storage` gerencia o upload e armazenamento de arquivos.

```php
use Core\Services\Storage;

$router->post('/upload', function () {
    $storage = app()->make(Storage::class);
    $filePath = $storage->storeFile($_FILES['file']['tmp_name'], $_FILES['file']['name']);
    return response()->json(['caminho' => $filePath]);
});
```

#### Serviço de Sessão

A classe `Session` gerencia dados de sessão normalmente localizados no $\_SESSION do PHP

```php
use Core\Services\Session;

$router->get('/session', function () {
    $session = app()->make(Session::class);
    $session->put('user_id', 123);
    return response()->json(['user_id' => $session->get('user_id')]);
});
```

---

### 6. Service Container e Providers

Anteriormente, mostramos o uso de `app()->make()` para criar instâncias de classes. Essa funcionalidade é fornecida pelo Service Container, que gerencia dependências e facilita a criação de objetos complexos.

#### O que é o Service Container?

O Service Container é uma ferramenta que ajuda a gerenciar dependências no seu código. Ele garante que cada serviço seja criado corretamente e, quando necessário, reutilizado em todo o sistema.

Por exemplo, ao criar uma conexão com o banco de dados, o Service Container pode garantir que apenas uma conexão seja aberta e reutilizada sempre que necessário.

Na prática, o Service Container funciona como uma "caixa de ferramentas" para o seu código. Imagine que uma parte do código diz: "Aqui está uma função que cria um objeto do tipo Request. Quando alguém precisar de um Request, execute essa função e entregue o resultado." Mais tarde, outra parte do código pode dizer: "Preciso de um objeto do tipo Request." O Service Container então executa a função que foi registrada anteriormente, cria o objeto e o entrega. Isso é útil porque você não precisa se preocupar em criar manualmente o objeto toda vez que precisar dele, nem em como ele é configurado. Além disso, se o objeto já foi criado antes, o container pode simplesmente reutilizá-lo, economizando recursos e evitando duplicação desnecessária.

**PARTE 1 DO CÓDIGO** – O Service Provider diz "Aqui está uma função que cria um objeto do tipo Request. Quando alguém precisar de um Request, execute essa função e entregue o resultado."

```php
use Core\Http\Request;

// Container é iniciado pela primeira vez, a caixa de ferramentas está vazia
$serviceContainer = Container::inicializar();

// note que Request::class é a mesma coisa que a string "Core\Http\Request"
$serviceContainer->singleton(Request::class, function() {
    return Request::createFromGlobals();
    // ou: return new Request($_GET, $_POST, $_FILES, $_SERVER, $_COOKIES);
});
```

A partir de agora, o Container tem um array mais ou menos assim:

```
$objetos = [
    "Core\Http\Request": $request_object
];
```

O container pegou a função, executou ela, e guardou dentro da lista de objetos

**PARTE 2 DO CÓDIGO** – Outra parte do código diz "Preciso de um objeto do tipo Request, entrega ele pra mim"

```
// Utilizar app() chama o Container que foi criado antes, e a função make chama a classe
$request = app()->make(Request::class);

var_dump($request->all());
```

#### Como Configurar o Service Container

A configuração do Service Container, ou seja, a "PARTE 1 DO CÓDIGO" com adição de novos objetos dentro do Container, é feita por meio de classes chamadas Providers. Providers instruem o container sobre como criar e gerenciar serviços.

#### Utilizando o AppServiceProvider padrão

1. Abra o arquivo `app/Providers/AppServiceProvider.php`.
2. Implemente a lógica de criação de serviços no método `register`.

Exemplo de Provider para configurar o banco de dados:

```php
namespace App\Providers;

use Core\Container\Container;
use Core\Providers\Provider;
use Core\Services\Database;
use Core\Support\Connection\Connection;

class AppServiceProvider extends Provider
{
    public function register()
    {
        // Quando alguém pedir a classe "Connection", execute a função pra criar um Connection,que nesse caso, pode ser um MySQLConnection ou um SQLiteConnection
        $this->app->singleton(Connection::class, function (Container $container) {
            // Aqui dentro, vamos decidir se vamos criar um MySQLConnection ou um SQLiteConnection.
            $config = config('database');
            $connectionType = $config['default'];

            if ($connectionType === 'mysql') {
                $mysqlConfig = $config['mysql'];
                return new MySQLConnection(
                    host: $mysqlConfig['connection']['host'],
                    username: $mysqlConfig['connection']['username'],
                    password: $mysqlConfig['connection']['password'],
                    database: $mysqlConfig['connection']['database'],
                    port: $mysqlConfig['connection']['port']
                );
            } elseif ($connectionType === 'sqlite') {
                $sqliteConfig = $config['sqlite'];
                return new SQLiteConnection(file: $sqliteConfig['file']);
            }
        });

        // Quando alguém pedir a classe "Database", entrega a função abaixo pra ele
        $this->app->singleton(Database::class, function (Container $container) {
            // Note que Database usa a classe Connection. Então daqui ele já fala "preciso de um objeto Connection" e executa a lógica acima pra decidir
            $connection = $container->make(Connection::class);

            return new Database($connection);
        });
    }
}
```

#### Registrando um Provider em outro arquivo

-   Também é possível criar uma nova classe na pasta `app\Providers`, evitando de ficar um arquivo gigante com todos os criadores de objetos.
-   Depois de criar a classe copiando AppServiceProvider e trocando o nome, registre-o no arquivo `config/providers.php`:

```php
return [
    App\Providers\AppServiceProvider::class,
    // Outros providers...
];
```

Note que se a classe criada não herdar da classe Provider, um erro ocorrerá

#### Usando o Service Container

Após configurar o Provider, você pode usar o Service Container para criar instâncias de serviços em qualquer parte do seu código, como nos Controllers ou nas rotas:

```php
$router->get('/users', function () {
    $database = app()->make(Database::class);
    return $database->fetch('SELECT * FROM users');
});
```

---

### 7. Configuração do Banco de Dados

O arquivo `config/database.php` define as configurações do banco de dados. O framework suporta MySQL e SQLite.

#### Exemplo de Configuração

```php
return [
    'default' => 'mysql',
    'mysql' => [
        'connection' => [
            'host' => '127.0.0.1',
            'username' => 'root',
            'password' => '',
            'database' => 'meu_banco',
            'port' => 3306,
        ],
        'migration' => 'database/migrations/mysql.sql',
    ],
    'sqlite' => [
        'file' => 'database/database.sqlite',
        'migration' => 'database/migrations/sqlite.sql',
    ],
];
```

#### Executando Migrações

Os scripts de migração estão localizados em `database/migrations`. Eles criam as tabelas necessárias no banco de dados e podem ser especificados no arquivo citado acima.

**IMPORTANTE: NUNCA inclua o CREATE DATABASE no script do MySQL, pois isso é feito automaticamente pelo framework e ao tentar criar a database duas vezes acontece um erro e as tabelas não são criadas.** Se precisar mesmo de um CREATE DATABASE, utilize CREATE DATABASE IF NOT EXISTS.

Segue abaixo exemplos para arquivos de migração em MySQL e SQLite

-   **MySQL**:

```sql
-- TABELAS
CREATE TABLE IF NOT EXISTS tb_contato(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    login VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefone VARCHAR(255) NOT NULL,
    foto VARCHAR(255)
);

-- INSERTS
INSERT INTO tb_contato VALUES
    (NULL, "Admin", "admin", "$2a$12$IYe6qvlevtzmCxu4zjkIIuLmrPMIvBwmhl3YApHE7fuxI9cadkesW", 'admin@gmail.com', '11951490211', NULL); -- Senha: admin
```

-   **SQLite**:

```sql
-- TABELAS
CREATE TABLE IF NOT EXISTS tb_contato(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    login TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    telefone TEXT NOT NULL,
    foto TEXT
);

-- INSERTS
INSERT INTO tb_contato VALUES
    (NULL, "Admin", "admin", "$2a$12$IYe6qvlevtzmCxu4zjkIIuLmrPMIvBwmhl3YApHE7fuxI9cadkesW", 'admin@gmail.com', '11951490211', NULL); -- Senha: admin
```
