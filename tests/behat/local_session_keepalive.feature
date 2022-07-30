@local @local_session_keepalive @javascript
Feature: Using session keepalive
  In order to be able to keep the session alive
  As admin
  I need to be able to configure the local_session_keepalive plugin

  Background:
    Given the following config values are set as admin:
      | config                | value |
      | sessiontimeout        | 120   |
      | sessiontimeoutwarning | 60    |
    And the following "users" exist:
      | username |
      | teacher1 |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Check session without enabling the plugin
    When I log in as "teacher1"
    And I am on site homepage
    And I wait "150" seconds
    And I am on "Course 1" course homepage
    Then I should see "You are not logged in"

  Scenario: Enable plugin and check that session will be kept alive
    Given the following config values are set as admin:
      | config            | value | plugin                  |
      | enable            | 1     | local_session_keepalive |
      | keepaliveinterval | 1     | local_session_keepalive |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | A forum |
    And I am on the "A forum" "forum activity" page
    And I click on "Add discussion topic" "link"
    And I set the following fields to these values:
      | Subject | Discussion subject |
      | Message | Discussion message |
    And I wait "80" seconds
    Then ".modal" "css_element" should not exist
    And I should not see "No recent activity"
    And I wait "80" seconds
    Then ".modal" "css_element" should not exist
    And I should not see "Session expired"
    And I click on "Post to forum" "button"
    And I should see "Discussion subject"

  Scenario: Enable plugin but do not enter anything in the form
    Given the following config values are set as admin:
      | config            | value | plugin                  |
      | enable            | 1     | local_session_keepalive |
      | keepaliveinterval | 1     | local_session_keepalive |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | A forum |
    And I am on the "A forum" "forum activity" page
    And I click on "Add discussion topic" "link"
    And I wait "80" seconds
    Then ".modal" "css_element" should exist
    And I should see "No recent activity" in the ".modal .modal-title" "css_element"
    And I wait "80" seconds
    Then ".modal" "css_element" should exist
    And I should see "Session expired" in the ".modal .modal-title" "css_element"
    And I click on "Cancel" "button" in the ".modal" "css_element"
    And ".modal" "css_element" should not be visible
    And I am on site homepage
    And I should see "You are not logged in"
