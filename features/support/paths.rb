require 'cucumber-wordpress'

module NavigationHelpers
  # Maps a name to a path. Used by the
  #
  #   When /^I go to (.+)$/ do |page_name|
  #
  # step definition in webrat_steps.rb
  #
  def path_to(page_name)
    path = WordPress.path_to(page_name)
    return path unless path.nil?
    URI::join("http://#{WordPress.WEBHOST}/", partial_path_to(page_name))
  end
  def partial_path_to(page_name)
    case page_name
    when /^edit post "(.+?)"$/
      id = WordPress.mysql.query(%Q'SELECT ID FROM #{WordPress.TABLE_PREFIX}posts WHERE post_title="#{$1}"').fetch_row.first.to_i
      "/wp-admin/post.php?action=edit&post=#{id}"
    else
      raise "Can't find mapping from \"#{page_name}\" to a path.\n"
    end
  end
end

World(NavigationHelpers)
