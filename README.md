# Installation

Update `composer.json` by adding this to the `repositories` array:

```json
{
    "type": "vcs",
    "url": "https://github.com/ohmediaorg/photo-bundle"
}
```

Then run `composer require ohmediaorg/photo-bundle:dev-main`.

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

<ul id="gallery_{{ uniq }}" class="gallery">
{% for photo in gallery.photos %}{% if loop.index <= 4 %}
<li class="gallery-item">
  <a href="#" class="gallery-photo">
    {{ image_tag(photo.image, {
      width: 300,
      height: 200,
    }) }}
  </a>
</li>
{% endif %}{% endfor %}
</ul>

<a href="#" id="gallery_{{ uniq }}_view_all" class="btn">View All Photos</a>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const items = [];

  {% for photo in gallery.photos %}
  {% set caption = photo.caption ?: '' %}
  items.push({
    src: {{ image_path(photo.image)|js }},
    caption: {{ caption|js }},
  });
  {% endfor %}

  function openFancybox(index) {
    new Fancybox(items, {
      startIndex: index,
    }, index);
  }

  const gallery = document.getElementById('gallery_{{ uniq }}');

  const photos = gallery.querySelectorAll('.gallery-photo');

  photos.forEach(function(photo, index) {
    photo.addEventListener('click', (e) => {
      e.preventDefault();

      openFancybox(index);
    });
  });

  const button = document.getElementById('gallery_{{ uniq }}_view_all');

  button.addEventListener('click', function(e) {
    e.preventDefault();

    openFancybox(0);
  });
});
</script>
```
