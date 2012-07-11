class CreateYoutube < ActiveRecord::Migration
  def self.up
    create_table :youtube do |t|
      t.string   "cms_headline"
      t.string   "link_text"
      t.string   "thumbnail_image"
      t.string   "align"
      t.text     "embed_html"
      t.boolean  "cms_active"
      t.boolean  "cms_deleted"
      t.boolean  "cms_draft"
      t.datetime "cms_created"
      t.datetime "cms_modified"
      t.integer  "cms_modified_by_user"
    end
  end
  def self.down
    drop_table :youtube
  end
end

