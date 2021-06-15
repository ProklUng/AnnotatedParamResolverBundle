# Бандл конвертации и валидации параметров контроллера через аннотации.

За основу взят [https://github.com/piku235/JungiFrameworkExtraBundle](бандл) и творчески доработан под
личные нужды. 

Например, кроме, DTO в виде stdClass поддерживаются SpatieDTO. Кое-что выпилено за ненадобностью (куки, заголовки и тому подобное).

### Установка

`composer require proklung/annotatedparamresolverbundle`

### Аннотации:

* **RequestBody** - Преобразует request body в аргумент контроллера (DTO).
* **RequestParams** - Преобразует POST параметры запроса в аргумент контроллера (DTO).
* **QueryParams** - Преобразует GET параметры запроса в аргумент контроллера (DTO).

Также поддерживаются аннотации PHP 8 (даже на версиях 7.2+). При использовании PHP 8 в дело пойдет нативный парсер
аннотаций.

### Общий принцип

`RequestBodyConverted` - пользовательский класс, реализующий (использующий интерфейс в качестве метки)
 `Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Contracts\UnserializableRequestInterface`.

В экземпляр этого класса после всех манипуляций лягут, в зависимости от аннотации, - GET, POST параметры 
запроса или сконвертированный request body.
 
Можно использовать [https://github.com/spatie/data-transfer-object](`Spatie DTO`), отнаследовавшись от `DataTransferObject`, и реализовав интерфейс `UnserializableRequestInterface`.

```php

use Prokl\AnnotatedParamResolverBundle\Annotation\RequestBody;
use Prokl\AnnotatedParamResolverBundle\Examples\RequestBodyConverted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExampleController extends AbstractController
{
    /**
     * Параметры аннотации необязательны!
     *
     * @param RequestBodyConverted $unserialized
     *
     * @return JsonResponse $content
     * @RequestBody(
            var="unserialized",
            class="Prokl\AnnotatedParamResolverBundle\Examples\RequestBodyConverted",
            validate=true
     )
     */
    public function action(
        RequestBodyConverted $unserialized
    ): JsonResponse {
        return new JsonResponse();
    }
}
```

Так тоже можно (валидация по умолчанию включена):

```php

use Prokl\AnnotatedParamResolverBundle\Annotation\RequestBody;
use Prokl\AnnotatedParamResolverBundle\Examples\RequestBodyConverted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExampleController extends AbstractController
{
    /**
     * Параметры аннотации необязательны!
     *
     * @param RequestBodyConverted $unserialized
     *
     * @return JsonResponse $content
     * @RequestBody()
     */
    public function action(
        RequestBodyConverted $unserialized
    ): JsonResponse {
        return new JsonResponse();
    }
}
```

### Валидация

В описании класса, реализующего `UnserializableRequestInterface` к свойствам класса допустимо
применять любые стандартные и кастомные валидаторы Symfony.

Опция validate в аннотации управляет валидацией, вне зависимости от аннотаций свойства класса.  

```php

use Prokl\AnnotatedParamResolverBundle\ArgumentResolver\Contracts\UnserializableRequestInterface;
use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Validator\Constraints as Assert;

class RequestBodyConvertedSpatie extends DataTransferObject implements UnserializableRequestInterface
{
    /**
     * @var string $email
     *
     * @Assert\Length(
     *  min=3,
     *  minMessage="Email must be at least {{ limit }} characters long"
     * )
     */
    public $email;

    /**
     * @var integer
     */
    public $numeric;
}
```

