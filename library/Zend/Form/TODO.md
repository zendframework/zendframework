Forms TODO
==========

BETA4 MUST HAVES
----------------

* CAPTCHA view helper
  * Accept an element, pull the "captcha" attribute, and call render() on the
    captcha
  * Potentially a "captcha" element, with a dedicated property?
* Remove "InputFilterAwareInterface" from forms

RFC Items
---------

* Builder component
  * Introspect a class for annotations and build a form
    * Each "element" can specify:
      * filters and validators to use to build an input for the input filter
      * attributes for use with the form element
  * Should be able to generate a form class

WISH LIST
---------

### View helpers

* HTML4/XHTML1 element types
  * formText(ElementInterface $element)
    * allows "dirname" -- directionality of the text
  * formButton(ElementInterface $element)
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
  * formReset(ElementInterface $element)
  * formSubmit(ElementInterface $element)
    * HTML5 allows "formaction", non-empty URL potentially surrounded by spaces
    * HTML5 allows "formenctype" ("application/x-www-form-urlencoded", "multipart/form-data", or "text/plain")
    * HTML5 allows "formmethod" ("get" or "post"
    * HTML5 allows "formtarget" (browsing-context name, or one of "\_blank", "\_self", "\_parent", or "\_top")
    * HTML5 allows "formnovalidate" ("formnovalidate" or "")
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



