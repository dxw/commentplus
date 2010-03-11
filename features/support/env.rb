require 'spec/mocks'
require 'webrat'
Webrat.configure do |config|
  config.mode = :mechanize
end
World do
  session = Webrat::Session.new
  session.extend(Webrat::Methods)
  session.extend(Webrat::Matchers)
  session
end

# WordPress stuff

require 'cucumber-wordpress'
require 'cucumber-wordpress/steps'
WordPress.configure(YAML::load(open(File.join(File.dirname(__FILE__),'config.yml'))))
WordPress.write_config do |config|
  config << <<HELO
function my_comment_flood_filter(){return 0;}
function wp_get_current_user() {
  add_filter('comment_flood_filter', 'my_comment_flood_filter');

  global $current_user; get_currentuserinfo(); return $current_user;
}
HELO
end
WordPress.create_db
at_exit do
  WordPress.reset_config
  WordPress.drop_db
end
Before do |scenario|
  WordPress.reset_db
end

$files = []
After do |scenario|
  while f = $files.pop
    File.delete f
  end
end

AfterStep do |scenario|
  Then 'I should not see "( ! )"' # Xdebug
  Then 'I should not see "WordPress database error"' # wpdb
  Then 'I should not see "Notice:  "'
  Then 'I should not see "Warning:  "'
  Then 'I should not see "Error:  "'
end
