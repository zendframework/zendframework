Forms TODO
==========

MUST HAVE for BETA4
-------------------

### View helpers

* formErrors(ElementInterface $element)
* formInput(ElementInterface $element)
  * this would look for a "type", and default to "text" if not found
* Normalization of common attributes (also, limit attributes to these?)
  * name
  * id
  * autocomplete ("on" or "off")
  * autofocus ("autofocus" or "")
  * disabled ("disabled" or "")
  * form (id of the form to which input is associated)
  * list (not sure how this works)
  * maxlength (non-negative integer - maximum allowed value length for element)
  * multiple ("multiple" or "")
  * pattern (JS regexp pattern that the value must fulfill)
  * placeholder (string without line breaks; short hint or phrase to aid user when entering data)
  * readonly ("readonly" or "")
  * required ("required" or "")
  * size (positive integer; number of options the control should show)
  * value

WISH LIST
---------

### View helpers

* HTML4/XHTML1 element types
  * formText(ElementInterface $element)
    * allows "dirname" -- directionality of the text
  * formButton(ElementInterface $element)
  * formCheckbox(ElementInterface $element)
    * allows 'checked="checked"' or 'checked=""'
  * formFile(ElementInterface $element)
    * allows "accept" (comma-separated MIME types)
  * formHidden(ElementInterface $element)
  * formImage(ElementInterface $element)
    * allows "alt", "height", and "width"
    * requires "src"
    * HTML5 allows "formaction", non-empty URL potentially surrounded by spaces
    * HTML5 allows "formenctype" ("application/x-www-form-urlencoded", "multipart/form-data", or "text/plain")
    * HTML5 allows "formmethod" ("get" or "post"
    * HTML5 allows "formtarget" (browsing-context name, or one of "\_blank", "\_self", "\_parent", or "\_top");
    * HTML5 allows "formnovalidate" ("formnovalidate" or "")
  * formPassword(ElementInterface $element)
  * formRadio(ElementInterface $element)
    * allows 'checked="checked"' or 'checked=""'
  * formReset(ElementInterface $element)
  * formSelect(ElementInterface $element)
    * allows "multiple"
    * Creates optgroup and option elements
      * option: "selected" ("selected" or ""), "disabled" ("disabled" or ""), "label", "value"
      * optgroup: "disabled" ("disabled" or ""), "label"
  * formSubmit(ElementInterface $element)
    * HTML5 allows "formaction", non-empty URL potentially surrounded by spaces
    * HTML5 allows "formenctype" ("application/x-www-form-urlencoded", "multipart/form-data", or "text/plain")
    * HTML5 allows "formmethod" ("get" or "post"
    * HTML5 allows "formtarget" (browsing-context name, or one of "\_blank", "\_self", "\_parent", or "\_top")
    * HTML5 allows "formnovalidate" ("formnovalidate" or "")
  * formTextarea(ElementInterface $element)
    * allows "rows", "cols"
    * HTML5 allows "dirname" -- directionality of the text
    * HTML5 allows "wrap" ("hard", "soft")
* HTML5 element types
  * formColor(ElementInterface $element)
    * allows `#[0-9a-fA-F]{6}`
  * formDate(ElementInterface $element)
    * min/max/value must be RFC3339 date (Y-m-d)
    * allows step (positive integer)
  * formDatetime(ElementInterface $element)
    * min/max/value must follow RFC3339, and T and Z (if used) in format MUST be uppercase
    * allows step (positive float)
  * formDatetimeLocal(ElementInterface $element)
    * min/max/value are of form <date>T<time>: 1985-04-12T23:20:40
    * allows step (positive float)
  * formEmail(ElementInterface $element)
    * value can actually be a single email or comma-separated list of emails if "multiple" is specified
  * formMonth(ElementInterface $element)
    * min/max/value must be in format "Y-m"
    * allows step (positive integer)
  * formNumber(ElementInterface $element)
    * should look for min/max (floating point)
    * allows step (positive float)
  * formRange(ElementInterface $element)
    * should look for min/max (floating point)
    * allows step (positive float)
  * formSearch(ElementInterface $element)
    * allows "dirname" -- directionality of the text
  * formTel(ElementInterface $element)
  * formTime(ElementInterface $element)
    * min/max/value must be in RFC3339 partial-time format (`H:i:m[.s]`)
    * allows step (positive float)
  * formUrl(ElementInterface $element)
  * formWeek(ElementInterface $element)
    * min/max/value must follow format "Y-W\<int\>": 1996-W16
    * allows step (positive integer)



