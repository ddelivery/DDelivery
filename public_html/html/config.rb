# Require any additional compass plugins here.

# Set this to the root of your project when deployed:
http_path = "/"
css_dir = "css"
sass_dir = "sass"
images_dir = "img"
javascripts_dir = "js"

# You can select your preferred output style here (can be overridden via the command line):
# output_style = :expanded or :nested or :compact or :compressed
output_style = :compact

# To enable relative paths to assets via compass helper functions. Uncomment:
relative_assets = true

# To disable debugging comments that display the original location of your selectors. Uncomment:
line_comments = false
sass_options = {:debug_info => false, :cache => true}

preferred_syntax = :scss

# this one for switching between production and dev versions of the website
module Sass::Script::Functions
  def image_path(string)
    assert_type string, :String
    Sass::Script::String.new("../img/#{string.value}")
  end
  alias_method :"image-path",:image_path 
  declare :"image-path", :args => [:string]
end
