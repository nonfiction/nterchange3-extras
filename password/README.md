## Password protected pages

Enables you to password protect pages. The password is hashed when saved and the
plaintext version is never stored.

### Usage

After adding the model/controller and creating the table, create your first
password 'group'.

    /nterchange/password/create

Then create a login page somewhere for this group with the following in a code
caller:

    {call controller=password action=login group=some_group}

Add the following code caller to any page you want to password protect:

    {call controller=password action=authenticate_for group=some_group}

> note: replace `some_group` with the name of the group you created

> note: please change $password->salt to something random in `models/password.php`
