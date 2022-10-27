# Game of Life

PHP implementation of [Game of Life](https://en.wikipedia.org/wiki/Conway%27s_Game_of_Life).

Application read initial word state from XML file defined as 1st parameter in run commnad. After iterations, the state of the world will be saved in an XML file defined as 2nd parameter.

## How to run application

1. Clone the repo and got to the application folder
2. Install and start application ```make up```
3. Install dependencies via composer: ```make composer-install```
4. Run the game: ```make input=/project/samples/input.xml play```
    * first parameter is required
    * second parameter is optional, default values is ```output.xml```

## Sample input
```xml
<?xml version="1.0" encoding="UTF­8"?>
<life>
    <world>
        <cells>4</cells> <!-- Dimension of the square "world" -->
        <iterations>10</iterations> <!-- Number of iterations to be calculated -->
    </world>
    <organisms>
        <organism>
            <x_pos>1</x_pos> <!-- x position -->
            <y_pos>2</y_pos> <!-- y position -->
        </organism>
        <organism>
            <x_pos>2</x_pos>
            <y_pos>2</y_pos>
        </organism>
        <organism>
            <x_pos>3</x_pos>
            <y_pos>2</y_pos>
        </organism>
    </organisms>
</life>
```

## How to run tests

Tests are written in [PHPUNIT package from Sebastian Bergmann](https://packagist.org/packages/phpunit/phpunit)

```
make tests 
```

## Static Analysis
Source code is fully valid with max level of PHPSTAN

```
make stan 
```

## Code style
Source code is written under standard PSR12

```
make cs
```


