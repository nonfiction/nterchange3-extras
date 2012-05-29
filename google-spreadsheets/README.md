Google Spreadsheet Integration
==============================

### Installing

Not sure why, but rsync doesn't seem to catch everything in lib/Zend so you may have to manually 
copy it.

Here's an example of storing and getting rows:

    require_once 'app/models/GSpreadsheet.php';
    function gdata() {
      $model = new GSpreadsheet('GData Spreadsheet', 'My Worksheet', 'user@gmail.com', 'password');
      
      // Create a new row from $_POST request
      $row = new GSpreadsheetRow($fields, $_POST);
    
      // Or pass in an array
      // $row = new GSpreadsheetRow($fields, array('Column 1'=>'Some stuff', 'column 2'=>'More stuff'));
    
      $vals = $row->data();
    
      // Insert the row if we have it
      if ($row->data()->someheadline && $row->data()->somedata) {
        $model->data = $row->data();
        $model->insert();
      }
      
      // Find all rows and set them for the view
      $model->find();
      $this->set('rows', $model->fetchAll());
    }
