Webcache
========

Store external feeds, processed data, or whatever you want
using MySQL as a key value store. This is a model-only asset meant
to be used by other assets for getting and setting data.

Install
-------

After uploading, run `rake` to create the webcache table in the DB.

Examples
--------

Grab some remote data

    $cache = &NModel::singleton('webcache');
    $data = $cache->wget('http://somedata.com/feed.xml', $ttl=5);
    
Or store some results

    $data = $cache->get('mydata', $ttl=1440);
    if (!$data){
      $data = some_time_consuming_stuff();
      $cache->set('mydata', $data);
    }
