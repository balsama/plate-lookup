# Boston Plate Lookup
Lookup outstanding Boston Massachusetts parking tickets by license plate.

```php
php ./scripts/lookupPlate.php <plate_number>
```

## What?
Boston doesn't make ticket data public. But it does allow you to look up tickets by entering a license plate and the birthday month/day of the registration holder. There doesn't seem to be any rate limiting on the form, so it's easy to brute force it by guessing the birthday month/day (it doesn't even require a year!)

I use it to look up [license plates that show up repeatedly in 311 compliants](https://twitter.com/balsama/status/1465186627257569281) to justify my ire.

Future plans are for a Twitter Bot that will respond with any found tickets when you tweet a license plate at it. But I imagine the City will figure this out before then and implement some sort of rate limiting on the form. 🤷‍♀️

## Usage
This wasn't really meant to be consumed. Look at the `Lookup` class if you're interested. Or:
1. Clone
2. Composer install
3. php ./scripts/lookupPlate.php <plate_number>

...will get you pretty output. Note that it can take a minute or two to run because we need to brute force the birthday month/day.