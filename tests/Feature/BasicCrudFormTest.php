<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;
use Tests\TestCase;
use Spatie\Browsershot\Browsershot;

class BasicCrudFormTest extends TestCase {

    use RefreshDatabase;

    private string $modelname = 'Subject';
    private string $tablename_plural = 'subjects';
    private bool $make_images = true;

    /**
     * Test that the welcome page is accessible.
     *
     * @return void
     */
    public function testThatWebsiteRespondsCorrectly(): void {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test that the home page requires a login.
     *
     * @return void
     */
    public function testThatWebsiteRequiresLoginForHome(): void {
        $response = $this->get('/home');
        $response->assertStatus(302);
    }

    /**
     * A basic test for logging in and finding /home.
     *
     * @return void
     */
    public function testAuthUserCanGetToHome(): void {

        $user = User::factory()->create();
        $this->assertEquals(1, User::count());

        Sanctum::actingAs($user);
        $response = $this->json('get', 'home');

        $response->assertStatus(200);
        $body = $response->getContent();

        $this->assertNotEmpty($body);
    }

    /**
     * Test for availability of Controller file.
     *
     * @return void
     */
    public function testExistenceOfControllerFile(): void {
        $this->assertFileExists(app_path('Http/Controllers/' . $this->modelname . 'Controller.php'));
    }

    /**
     * Test for availability of Model database table.
     *
     * @return void
     */
    public function testExistenceOfDatabaseTable(): void {

        // Test that the user exists in the database
        $this->assertTrue(Schema::hasTable($this->tablename_plural));
        // $this->assertDatabaseCount($this->tablename_plural, 0);
    }

    /**
     * A test for making sure that NO operations are possible without login.
     *
     * @return void
     */
    public function testUnAuthUserCannotGetToCrud(): void {

        $user = User::factory()->create();
        $subject = Subject::factory()->create();

        $response = $this->get($this->tablename_plural);
        $response->assertStatus(302, 'Unauthenticated user should not be able to access ' . $this->tablename_plural);

        $response = $this->get($this->tablename_plural . '/' . $subject->id);
        $response->assertStatus(302,
            'Unauthorized user should not be able to access ' . $this->tablename_plural . '/show/' . $subject->id);

        $response = $this->get($this->tablename_plural . '/' . $subject->id . '/edit');
        $response->assertStatus(302,
            'Unauthorized user should not be able to edit ' . $this->tablename_plural . '/show/' . $subject->id);

        $response = $this->post($this->tablename_plural);
        $response->assertStatus(302, 'Unauthorized user should not be able to access ' . $this->tablename_plural . '/create');

        $response = $this->put($this->tablename_plural . '/' . $subject->id);
        $response->assertStatus(302,
            'Unauthorized user should not be able to alter ' . $this->tablename_plural . '/show/' . $subject->id);

        $response = $this->delete($this->tablename_plural . '/' . $subject->id);
        $response->assertStatus(302,
            'Unauthorized user should not be able to delete ' . $this->tablename_plural . '/show/' . $subject->id);

    }

    /**
     * A test for analysing contents of welcome page.
     *
     * @return void
     */
    public function testViewContentsWelcome(): void {
        $contents = (string) $this->view('welcome');
        $this->assertStringContainsString('Laravel', $contents);
    }

    /**
     * A test to check that index page has required UI elements and renders data without error.
     *
     * @return void
     * @throws CouldNotTakeBrowsershot
     */
    public function testViewContentsIndexHasNeededElements(): void {
        $subjects = Subject::factory()->count(100)->create();
        $this->assertDatabaseCount('subjects', 100);
        $contents = (string) $this->view('subjects.index', ['subjects' => $subjects]);
        $this->assertStringContainsString($subjects[0]->name, $contents);
        $this->make_images ? Browsershot::html($contents)->save('testimage-' . __LINE__ . '.pdf') : null;
        $this->assertStringContainsString(substr($subjects[0]->description, 0, 20), $contents);
    }

    /**
     * A test to check that index page can be called through controller, has required UI elements and renders data without error.
     *
     * @return void
     * @throws CouldNotTakeBrowsershot
     */
    public function testControllerViewContentsIndexHasNeededElements(): void {
        // Fill in some test data
        $subjects = Subject::factory()->count(100)->create();
        $this->assertDatabaseCount('subjects', 100);

        // Create a user and log in
        $user = User::factory()->create();
        $this->assertDatabaseCount('users', 1);
        Sanctum::actingAs($user);

        // Get the index page and run content tests
        $view = $this->get(route('subjects.index'));
        $this->make_images ? Browsershot::html($view->getContent())->save('testimage-' . __LINE__ . '.pdf') : null;
        $view->assertSee($subjects[0]->name);
        $view->assertSee(substr($subjects[0]->description, 0, 20));
    }

    /**
     * A test to check that detail show page has required UI elements and renders data without error.
     *
     * @return void
     * @throws CouldNotTakeBrowsershot
     */
    public function testViewContentsShowHasNeededElements(): void {
        $subject = Subject::factory()->create();
        $this->assertDatabaseCount('subjects', 1);
        $contents = (string) $this->view('subjects.show', ['subject' => $subject]);
        $this->assertStringContainsString($subject->name, $contents);
        $this->make_images ? Browsershot::html($contents)->save('testimage-' . __LINE__ . '.pdf') : null;
        $this->assertStringContainsString($subject->id, $contents);
        $this->assertStringContainsString($subject->description, $contents);
        $this->assertStringContainsString(__('Back'), $contents);
    }

    /**
     * A test to check that detail show page can be processed via controller method, has required UI elements and renders data without error.
     *
     * @return void
     * @throws CouldNotTakeBrowsershot
     */
    public function testControllerViewContentsShowHasNeededElements(): void {
        // Fill in some test data
        $subjects = Subject::factory()->count(100)->create();
        $this->assertDatabaseCount('subjects', 100);

        // Create a user and log in
        $user = User::factory()->create();
        $this->assertDatabaseCount('users', 1);
        Sanctum::actingAs($user);

        // Get the index page and run content tests
        $view = $this->get(route('subjects.show', $subjects[0]->id));
        $this->make_images ? Browsershot::html($view->getContent())->save('testimage-' . __LINE__ . '.pdf') : null;
        $view->assertSee('Back', 'Back button not found');
        $view->assertSee($subjects[0]->name, 'Name not found');
        $view->assertSee($subjects[0]->description, 'Description not found');

        // Make sure we can't access a non-existent subject
        $view = $this->get(route('subjects.show', 1000));
        $view->assertStatus(404, 'Should not be able to access a non-existent subject');
    }

    /**
     * A test to check that detail show page has required UI elements and renders data without error.
     *
     * @return void
     * @throws CouldNotTakeBrowsershot
     */
    public function testViewContentsEditHasNeededElements(): void {
        $subject = Subject::factory()->create();
        $this->assertDatabaseCount('subjects', 1);
        $contents = (string) $this->view('subjects.edit', ['subject' => $subject]);
        $this->make_images ? Browsershot::html($contents)->save('testimage-' . __LINE__ . '.pdf') : null;
        $this->assertStringContainsString($subject->name, $contents);
        $this->assertStringContainsString($subject->description, $contents);
        $this->assertStringContainsString(__('Update'), $contents);
        $this->assertStringContainsString(__('Back'), $contents);
    }

     /**
     * A test showing that edit view can be called via controller and has required UI elements and renders data without error.
     *
     * @return void
     * @throws CouldNotTakeBrowsershot
     */
    public function testControllerViewContentsEditHasNeededElements(): void {
        // Fill in some test data
        $subjects = Subject::factory()->count(100)->create();
        $this->assertDatabaseCount('subjects', 100);

        // Create a user and log in
        $user = User::factory()->create();
        $this->assertDatabaseCount('users', 1);
        Sanctum::actingAs($user);

        // Get the index page and run content tests
        $view = $this->get(route('subjects.edit', $subjects[0]->id));
        $this->make_images ? Browsershot::html($view->getContent())->save('testimage-' . __LINE__ . '.pdf') : null;
        $view->assertSee('Back', 'Back button not found');
        $view->assertSee($subjects[0]->name, 'Name not found');
        $view->assertSee($subjects[0]->description, 'Description not found');

        $view = $this->get(route('subjects.show', 1000));
        $view->assertStatus(404, 'Should not be able to access a non-existent subject');
    }

    /**
     * A test showing that a new record can be edited and submitted, with persistence impact.
     *
     * @return void
     */
    public function testEditFormChangesArePropagatedIntoDatabase(): void {

        // Fill in some test data
        $subject = Subject::factory()->create();
        $this->assertDatabaseCount('subjects', 1);

        // Create a user and log in
        $user = User::factory()->create();
        $this->assertDatabaseCount('users', 1);
        Sanctum::actingAs($user);
        $namebefore = $subject->name;

        $response = $this->from('/subjects/1/edit')->put('/subjects/1', [
            'name' => 'newname',
            'description' => 'newdescription',
        ]);

        // Redirect to the index page
        $response->assertStatus(302);
        $response->assertRedirect('/subjects');

        Subject::first()->refresh();

        $this->assertNotEquals($namebefore, Subject::first()->name);
        $this->assertEquals('newname', Subject::first()->name);
        $this->assertEquals('newdescription', Subject::first()->description);
    }

    /**
     * A test that create form has required UI elements and renders data without error.
     *
     * @return void
     * @throws CouldNotTakeBrowsershot
     */
    public function testViewContentsCreateHasNeededElements(): void {
        $contents = (string) $this->view('subjects.create');
        $this->make_images ? Browsershot::html($contents)->save('testimage-' . __LINE__ . '.pdf') : null;
        $this->assertStringContainsString(__('Save'), $contents);
        $this->assertStringContainsString(__('Back'), $contents);
    }


    /**
     * A test to check the create page can be processed via controller method, has required UI elements and renders data without error.
     *
     * @return void
     * @throws CouldNotTakeBrowsershot
     */
    public function testControllerViewContentsCreateHasNeededElements(): void {
        // Fill in some test data
        $subjects = Subject::factory()->count(100)->create();
        $this->assertEquals(100, Subject::count());

        // Create a user and log in
        $user = User::factory()->create();
        $this->assertEquals(1, User::count());
        Sanctum::actingAs($user);

        // Get the index page and run content tests
        $view = $this->get(route('subjects.create'));
        $this->make_images ? Browsershot::html($view->getContent())->save('testimage-' . __LINE__ . '.pdf') : null;
        $view->assertSee('Back', 'Back button not found');
        $view->assertSee('Save', 'Save button not found');
        $view->assertStatus(200, 'Status should be 200');
    }

    /**
     * Test that new entries can be created via the controller methods.
     *
     * @return void
     */
    public function testCreateFormSubmitsArePropagatedIntoDatabase(): void {

        // Create a user and log in
        $user = User::factory()->create();
        $this->assertDatabaseCount('users', 1);
        Sanctum::actingAs($user);

        $response = $this->from('/subjects/create')->post('/subjects', [
            'name' => 'createdname',
            'description' => 'createddescription',
        ]);

        // Redirect to the index page
        $response->assertStatus(302);
        $response->assertRedirect('/subjects');

        $this->assertEquals('createdname', Subject::first()->name);
        $this->assertEquals('createddescription', Subject::first()->description);
    }

    /**
     * Test that deletion is accessible and works
     *
     * @return void
     */
    public function testDeleteButtonDoesJobAndRemovesEntryFromDatabase(): void {

        // Fill in some test data
        $subject = Subject::factory()->create();
        $this->assertDatabaseCount('subjects', 1);

        // Create a user and log in
        $user = User::factory()->create();
        $this->assertDatabaseCount('users', 1);
        Sanctum::actingAs($user);

        $response = $this->from('/subjects')->delete('/subjects/1');

        // Redirect to the index page
        $response->assertStatus(302);
        $response->assertRedirect('/subjects');

        // Now, subject should be gone
        $this->assertDatabaseCount('subjects', 0);

    }


}
