document.addEventListener('DOMContentLoaded', () => {
  tinymce.init({
    selector: '#article_content',
    image_dimensions: true,
    object_resizing: 'img',
    plugins: [
      'anchor', 'autolink', 'charmap', 'codesample', 'emoticons',
      'link', 'lists', 'media', 'searchreplace', 'table',
      'visualblocks', 'wordcount', 'image'
    ],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | image',
    automatic_uploads: false, // 🔴 désactive l'upload immédiat
    images_upload_handler: async (blobInfo) => {
      // ce handler reste défini mais ne sera appelé que manuellement
      const fd = new FormData();
      fd.append('file', blobInfo.blob(), blobInfo.filename());
      fd.append('width',  blobInfo._width  ?? 0);
      fd.append('height', blobInfo._height ?? 0);

      const res = await fetch('/backoffice/?action=article_image_upload', {
        method: 'POST',
        body: fd,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        credentials: 'same-origin'
      });

      const data = await res.json();
      if (!res.ok || !data.success || !data.location) {
        throw new Error(data.message || 'Upload image échoué');
      }

      return data.location;
    }
  });

  const form = document.querySelector('form[action="/backoffice/?action=article_add_save"]');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const editor = tinymce.get('article_content');
    if (!editor) return;

    // 1. Récupérer toutes les images blob dans l'éditeur
    const images = [...editor.dom.select('img[src^="blob:"], img[src^="data:"]')];

    for (const img of images) {
      const blobSrc = img.getAttribute('src');

      // Dimensions appliquées par l'utilisateur dans l'éditeur
      const width  = img.width  || img.naturalWidth  || 0;
      const height = img.height || img.naturalHeight || 0;

      try {
        // Convertir le blob src en fichier uploadable
        const blob = await fetch(blobSrc).then(r => r.blob());
        const filename = 'image.' + (blob.type.split('/')[1] || 'jpg');

        const fd = new FormData();
        fd.append('file', blob, filename);
        fd.append('width',  width);
        fd.append('height', height);

        const res = await fetch('/backoffice/?action=article_image_upload', {
          method: 'POST',
          body: fd,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          },
          credentials: 'same-origin'
        });

        const data = await res.json();
        if (!res.ok || !data.success || !data.location) {
          throw new Error(data.message || 'Upload échoué');
        }

        // Remplacer le blob src par l'URL définitive
        img.setAttribute('src', data.location);

      } catch (err) {
        alert('Erreur upload image : ' + err.message);
        return; // stopper la soumission si un upload échoue
      }
    }

    // 2. Sync le contenu final (avec les vraies URLs) dans le textarea
    editor.save();

    // 3. Récupérer les métadonnées des images finales
    const allImages = [...editor.dom.select('img[src]')];
    const imagesMeta = allImages
      .map((img) => ({
        local_cache: (img.getAttribute('src') || '').trim(),
        alt: (img.getAttribute('alt') || '').trim()
      }))
      .filter((img) => img.local_cache !== '');

    // 4. Soumettre
    const formData = new FormData(form);
    formData.append('images_meta', JSON.stringify(imagesMeta));

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      });

      const data = await response.json();

      if (!response.ok || !data.success) {
        alert(data.message || 'Erreur lors de l\'enregistrement.');
        return;
      }

      alert(data.message || 'Article enregistré.');
      form.reset();
      editor.setContent('');

    } catch (err) {
      alert('Erreur réseau, merci de réessayer.');
    }
  });
});