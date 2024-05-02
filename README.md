# Installation

Update `composer.json` by adding this to the `repositories` array:

```json
{
    "type": "vcs",
    "url": "https://github.com/ohmediaorg/photo-bundle"
}
```

Then run `composer require ohmediaorg/photo-bundle:dev-main`.

Enable the bundle in `config/bundles.php`:

```php
OHMedia\PhotoBundle\OHMediaPhotoBundle::class => ['all' => true],
```

Import the routes in `config/routes.yaml`:

```yaml
oh_media_photo:
    resource: '@OHMediaPhotoBundle/config/routes.yaml'
```

Run `php bin/console make:migration` then run the subsequent migration.

# Frontend

Create `templates/bundles/OHMediaPhotoBundle/gallery.html.twig`.

## Fancybox Integration

Run `npm install @fancyapps/ui`.

Import the appropriate files in `assets/frontend/frontend.js`:

```js
import { Fancybox } from '@fancyapps/ui';
import '@fancyapps/ui/dist/fancybox/fancybox.css';
window.Fancybox = Fancybox;
```

Display a limited gallery:

```twig
{# templates/bundles/OHMediaPhotoBundle/gallery.html.twig #}

{% set uniq = uniq() %}

<ul id="gallery_{{ uniq }}">
{% for photo in gallery.photos if loop.index <= 4 %}
<li>
  <a href="#" >
    {{ image_tag(photo.image, {
      width: ...,
      height: ...,
    }) }}
  </a>
</li>
{% endfor %}
</ul>

<a href="#" id="gallery_{{ uniq }}_view_all" class="btn">View All Photos</a>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const items = [];

  {% for photo in gallery.photos if loop.index <= 4 %}
  {% set caption = photo.caption ? photo.caption : '' %}
  items.push({
    src: {{ image_path(photo.image)|json_encode|raw }},
    opts: {
      caption: {{ caption|json_encode|raw }},
    }
  });
  {% endfor %}

  let fancybox = null;

  function openFancybox(index) {
    if (!fancybox) {
      fancybox = new Fancybox(items, {}, index);
    } else {
      fancybox.jumpTo(index);
    }
  }

  const fancybox = new Fancybox(items);

  const gallery = document.getElementById('gallery_{{ uniq }}');

  const photos = gallery.querySelectorAll('li > a');

  photos.forEach(function(photo, index) {
    photo.addEventListener('click', (e) => {
      e.preventDefault();

      openFancybox(index);
    });
  });

  const button = document.getElementById('view-all-photos');

  button.addEventListener('click', function(e) {
    e.preventDefault();

    openFancybox(0);
  });
});
</script>
```
