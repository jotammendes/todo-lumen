<?php

namespace Features\app\Http\Controllers;

use App\Models\Todo;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class TodoControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testUserCanListTodos()
    {
        // Prepare
        $todos = Todo::factory()->count(5)->create();

        // Act
        $response = $this->get('/todos');

        // Assert
        $response->assertResponseStatus(200); // 200 = OK
        $response->seeJsonContains(['current_page' => 1]);
    }

    public function testUserCanCreateATodo()
    {
        // Prepare
        $payload = [
          'title' => 'Tirar o lixo',
          'description' => 'Não esquecer de tirar o lixo amanhã às 11h'
        ];

        // Act
        $response = $this->post('/todos', $payload);

        // Assert
        $response->assertResponseStatus(201); // 201 = CREATED
        $response->seeInDatabase('todos', $payload);
    }

    public function testUserShouldSendTitleAndDescription()
    {
        // Prepare
        $payload = [
          'name' => 'John Doe',
        ];

        // Act
        $response = $this->post('/todos', $payload);

        // Assert
        $response->assertResponseStatus(422); // 422 = UNPROCESSABLE ENTITY
    }

    public function testUserCanRetrieveASpecificTodo()
    {
        // Prepare
        $todo = Todo::factory()->create();

        // Act
        $url = '/todos/' . $todo->id;
        $response = $this->get($url);

        // Assert
        $response->assertResponseOk(); 
        $response->seeJsonContains(['title' => $todo->title]);
    }

    public function testUserShouldReceive404WhenSearchSomethingThatDoesntExists()
    {
        // Prepare

        // Act
        $response = $this->get('/todos/2');

        // Assert
        $response->assertResponseStatus(404); // 404 = NOT FOUND
        $response->seeJsonContains(['error' => 'Not found']);
    }

    public function testUserCanDeleteATodo()
    {
        // Prepare
        $todo = Todo::factory()->create();

        // Act
        $url = '/todos/' . $todo->id;
        $response = $this->delete($url);

        // Assert
        $response->assertResponseStatus(204); // 204 = NO CONTENT
        $response->notSeeInDatabase('todos', ['id' => $todo->id]);
    }

    public function testUserCanSetTodoDone()
    {
        // Prepare
        $todo = Todo::factory()->create();

        // Act
        $url = '/todos/' . $todo->id . '/done';
        $response = $this->put($url);

        // Assert
        $response->assertResponseStatus(200); // 200 = OK
        $response->seeInDatabase('todos', ['id' => $todo->id, 'done' => true]);
    }

    public function testUserCanSetTodoUndone()
    {
        // Prepare
        $todo = Todo::factory()->create(['done' => true]);

        // Act
        $url = '/todos/' . $todo->id . '/undone';
        $response = $this->put($url);

        // Assert
        $response->assertResponseStatus(200); // 200 = OK
        $response->seeInDatabase('todos', ['id' => $todo->id, 'done' => false]);
    }
}
