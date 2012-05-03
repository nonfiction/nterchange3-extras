class CreateWebcache < ActiveRecord::Migration
  def self.up
    create_table :webcache do |t|
      t.string    "cms_headline"
      t.timestamp "mtime"
      t.text      "data"
    end
  end
  def self.down
    drop_table :webcache
  end
end
