# no.maf.generic
CiviCRM extension with MAF Norge specific config items and generic functionality.

## Provided functionality

* Fixes the calculations of the deductible amount in the Summary Fields extension. In the summary 
fields extension the deductible amount is calculated as a sum of the total amounts of contributions linked to 
financial types with the checkbox deductible checked. This extension fixes that calculation by taking the non deductible amount
into consideration.

