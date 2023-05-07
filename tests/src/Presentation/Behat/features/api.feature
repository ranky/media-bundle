Feature: Media Bundle API

  In order to perform CRUD operations on our API of media
  As a user logged in as "jcarlos"
  I want to be able to create, read, update and delete files from our API of media
  In addition to being able to make use of the query filters to filter the result of media in the listing
  So, to fulfill some of our scenarios, before starting the feature, a record will be created in our database with id 01GGM122J0DA8NKJ49FW56RH7E

  Background:
    Given I am logged in as "jcarlos"

  Scenario: Media Listing
    When I send a "GET" request to "/ranky/media"
    Then the response status code should be 200
    And the response JSON expression match "pagination.count" == "1" as "int"
    And the response JSON expression match "result[*].id" contains "01GGM122J0DA8NKJ49FW56RH7E"

  Scenario: Filters media listing by the image mime type
    When I send a "GET" request to "/ranky/media?filters[mime][starts]=image"
    Then the response status code should be 200
    And the response JSON expression match "result[*].id" contains "01GGM122J0DA8NKJ49FW56RH7E"
    And the JSON response key "pagination" should exist

  Scenario: Filters media listing by jcarlos user
    When I send a "GET" request to "/ranky/media?filters[createdBy][eq]=jcarlos"
    Then the response status code should be 200
    And the response JSON expression match "result[*].id" contains "01GGM122J0DA8NKJ49FW56RH7E"
    And the JSON response key "pagination" should exist

  Scenario: Filters media listing by Ulid
    When I send a "GET" request to "/ranky/media?filters[id][eq]=01GGM122J0DA8NKJ49FW56RH7E"
    Then the response status code should be 200
    And the response JSON expression match "result[*].id" contains "01GGM122J0DA8NKJ49FW56RH7E"
    And the JSON response key "pagination" should exist

  Scenario: Filters media listing by the video mime
    When I send a "GET" request to "/ranky/media?filters[mime][starts]=video"
    Then the response status code should be 200
    And the JSON response key "result" should be empty

  Scenario: Filters media listing by non-existing filter
    When I send a "GET" request to "/ranky/media?filters[random][starts]=random"
    Then the response status code should be 400
    And the response JSON expression match "type" == "Bad Request"
    And the response header "Content-Type" should be equal to "application/problem+json"

  Scenario: Get media configuration
    When I send a "GET" request to "/ranky/media/config"
    Then the response status code should be 200
    And the JSON response key "translations" should exist
    And the JSON response key "config" should include:
    """
    {
        "pagination_limit": 30,
        "upload_url": "/uploads",
        "max_file_size": 7340032,
        "locale": "es"
    }
    """

  Scenario: Get media form filters
    When I send a "GET" request to "/ranky/media/filters"
    Then the response status code should be 200
    And the JSON response key "availableDates" should exist
    And the JSON response key "mimeTypes" should exist
    And the JSON response key "users" should exist

  Scenario: Create media with Gans-of-London.jpg file
    Given I set "content-type" header equal to "multipart/form-data; boundary=--abcd64"
    And I attach the file "Gans-of-London.jpg" to request with key "file"
    When I send a "POST" request to "/ranky/media"
    Then the response status code should be 200
    And the response JSON expression match "file.name" == "gans-of-london.jpg"
    And the response JSON expression match "createdBy" == "Jcarlos"

  Scenario: Update media with Ulid 01GGM122J0DA8NKJ49FW56RH7E
    When I send a "PUT" request to "/ranky/media/01GGM122J0DA8NKJ49FW56RH7E" with body:
    """
    {
      "id": "01GGM122J0DA8NKJ49FW56RH7E",
      "alt": "New Alt",
      "title": "New Title",
      "name": "new-name"
    }
    """
    Then the response status code should be 200
    And the response JSON expression match "updatedBy" == "Jcarlos"
    And the response JSON expression match "description.title" == "New Title"
    And the response JSON expression match "description.alt" == "New Alt"
    And the response JSON expression match "file.basename" == "new-name"

  Scenario: Show media with Ulid 01GGM122J0DA8NKJ49FW56RH7E
    When I send a "GET" request to "/ranky/media/01GGM122J0DA8NKJ49FW56RH7E"
    Then the response status code should be 200
    And the response JSON expression match "id" == "01GGM122J0DA8NKJ49FW56RH7E"

  Scenario: Delete media with Ulid 01GGM122J0DA8NKJ49FW56RH7E
    When I send a "DELETE" request to "/ranky/media/01GGM122J0DA8NKJ49FW56RH7E"
    Then the response status code should be 200
    And the JSON response key "message" should exist

  Scenario: Not found media with Ulid 01GGM122J0DA8NKJ49FW56RH7E
    When I send a "GET" request to "/ranky/media/01GGM122J0DA8NKJ49FW56RH7E"
    Then the response status code should be 404
    And the response JSON expression match "type" == "Not Found"
    And the response header "Content-Type" should be equal to "application/problem+json"
