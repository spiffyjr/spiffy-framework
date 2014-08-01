Feature: homepage
  As a framework user
  I want to load the homepage
  To ensure integration is complete

  Scenario: Requesting the homepage
    Given the route "home" for path "/" exists with action "home"
    And the action "home" exists
    When a request to "/" is made
    Then I should get a 200 response code
