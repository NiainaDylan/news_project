document.addEventListener('DOMContentLoaded', () => {
  tinymce.init({
    selector: '#article_content',
    plugins: [
      'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount'
    ],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat'
  });

  const form = document.querySelector('form[action="/backoffice/?action=article_add_save"]');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    tinymce.triggerSave();

    const formData = new FormData(form);

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
      });

      const data = await response.json();

      if (!response.ok || !data.success) {
        alert(data.message || 'Erreur lors de l’enregistrement.');
        return;
      }

      alert(data.message || 'Article enregistre.');
      form.reset();
        if (tinymce.get('article_content')) {
            tinymce.get('article_content').setContent('');
        }
    } catch (err) {
      alert('Erreur reseau, merci de reessayer.');
    }
  });
});