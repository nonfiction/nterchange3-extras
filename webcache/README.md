Webcache
========

Store external feeds, processed data, or whatever you want
using the Nterchange Cache as a key value store.

Examples
--------

Remote data methods

    $cache = &NModel::singleton('webcache');
    $data = $cache->wget('http://somedata.com/feed.xml', $ttl=5);

    // or use getXML() which returns a DOM tree
    $data = $cache->getXML('http://pinecliffenergy.mwnewsroom.com/xml-feed', 0.1);
    
Key/value store methods

    $cache = &NModel::singleton('webcache');
    $data = $cache->get('mydata', $ttl=1440);
    if (!$data){
      $data = some_time_consuming_stuff();
      $cache->set('mydata', $data);
    }
