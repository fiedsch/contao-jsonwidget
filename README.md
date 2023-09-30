# JSON and YAML Widgets for Contao

The `jsonWidget` can be used in DCA files to create a text field that contains a JSON string.
While saving it will be checked if that the string is valid JSON. 
The widget displays the JSON string with `JSON_PRETTY_PRINT` so that checking/finding errors 
is easier for the user.

The `yamlWidget` is mostly the same, except that it uses the YAML format.
  

## Example: extending Members

### DCA

```php
$GLOBALS['TL_DCA']['tl_member']['fields']['json_data'] = [
   'inputType' => 'jsonWidget',
   'label'     => &$GLOBALS['TL_LANG']['tl_member']['json_data'],
   'eval'      => ['tl_style' => 'long', 'decodeEntities' => true], 
   'sql'       => "blob NULL",
 ];
 
 // Add json_data to $GLOBALS['TL_DCA']['tl_member']['palettes']['default'] 
 // where ever you like
 ```
Other valid options in `eval` are the same as for `textarea`s (as `WidgetJSON` extends `TextArea`), 
except that setting `rte` will be ignored because the editors provided do not make sense here. 


### How to use the JSON data?

Extend `tl_member` as in the above example. Then create an `ExtendedMemberModel` that 
extends Contao's `MemberModel`. In the magic methodd `__set()` and `_get` you can intercept
the "fields" stored in `json_data`. The `Fiedsch\JsonWidgetBundle\JsonGetterSetterTrait` takes 
care of that:

```php
// models/ExtendedMemberModel.php
namespace Contao;

use Fiedsch\JsonWidgetBundle\Traits\JsonGetterSetterTrait;

class ExtendedMemberModel extends MemberModel
{
    // let __set() and __get() take care of the JSON or YAML data (both at the same time will not work!)
    use JsonGetterSetterTrait;
    // or (see above!)
    use YamlGetterSetterTrait;

  /**
    * The column name we selected for the `jsonWidget` in the example above
    * @var string
    */
    protected static $strJsonColumn = 'my_json_data_column';
    
    /**
      * Same thing for the `yamlWidget`
      * @var string
      */
    protected static $strYamlColumn = 'my_yaml_data_column';

}
```

```php
// config/config.php
$GLOBALS['TL_MODELS']['tl_member'] = 'Contao\ExtendedMemberModel';
```


### Using the Model

```php
$member = \ExtendedMemberModel::findById(42);

// access fields columns created by contao's default DCA
printf("read member %s %s\n", $member->firstname, $member->lastname);

// access a field stored in our JSON data column
printf("transparently accessing a field from the JSON data ... '%s'\n", $member->whatever);

// set values and store in database
$member->a_key_for_a_scalar_value = "fourtytwo";
$member->key_for_an_array = ['an','array','containing','some','strings'];
$member->save(); // Note that saving will lose comments in your YAML-data 
                 // as Symfony\Component\Yaml\Yaml will not save them 
```


### YAML-Syntax Highlighting with ACE

Set 
```php
'eval' => [ /* ... , */ 'rte'=>'ace|yaml'],
```
in your field's DCA definitions.

Quick and dirty way: add desired CSS-Rules like e.g. 
```css
.ace_comment {
  color: red !important;
}
```
to your `be_ace.html5` (which you create it if you do not yet have a custom version).
OR: use a custom backend style and add the CSS rules there.


### Set JSON (or JAML) Data 

If you want to set all the JSON (or YAML) data at once you cannot use
```php
# In this example we assume $strJsonColumn = 'json_data';
$data = [ 'foo' => 1, 'bar' => 'baz ];
$model->json_data = $data; # will throw an exception
```
To do this you have to use
```php
$data = [ 'foo' => 1, 'bar' => 'baz ];
$model->setJsonColumnData(array $data);
# or
# $model->setYamlColumnData(array $data);
```
Note that this way previously set JSON (or YAML) data is overwritten.

Also note that the behaviour of `setJsonColumnData([])` (when setting an empty data array) changed in version `0.5.0`. Previously  it created `[]` where it 
now creates `{}` in the respective database column. 

In version `0.7.0` we slightly changed this again to achieve the original goal: The storage of empty arrays as object (`{}`) is only enforced on the top level 
of the data. In lower levels, empty arrays will be stored as arrays (`[]`) not as "empty" objects (`{}`): compare
`{ "foo": [] }` (versions >= 0.7.0) to `{ "foo": {} }` (versions >=0.5.0 and <0.7.0). 
```