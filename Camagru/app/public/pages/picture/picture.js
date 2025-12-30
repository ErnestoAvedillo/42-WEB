document.addEventListener("DOMContentLoaded", function () {
    const likeButton = document.querySelector(".like_button");
    const dislikeButton = document.querySelector(".dislike_button");
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Verificar que el CSRF token exista
    if (!csrfToken) {
        console.error('CSRF token not found');
        return;
    }

    function updateLikeDislikeButtons(data) {
        // Actualizar contadores
        const likeCount = likeButton.querySelector('.like-count');
        const dislikeCount = dislikeButton.querySelector('.dislike-count');
        const likeIcon = likeButton.querySelector('.like-icon');
        const dislikeIcon = dislikeButton.querySelector('.dislike-icon');
        
        if (likeCount) likeCount.textContent = `(${data.like_count})`;
        if (dislikeCount) dislikeCount.textContent = `(${data.dislike_count})`;
        
        // Actualizar iconos y estados basados en la reacción del usuario
        if (data.user_reaction === true) {
            // Usuario dio like
            likeIcon.textContent = '👍';
            dislikeIcon.textContent = '👎︎';
            likeButton.setAttribute('data-liked', 'true');
            likeButton.setAttribute('aria-pressed', 'true');
            dislikeButton.setAttribute('data-disliked', 'false');
            dislikeButton.setAttribute('aria-pressed', 'false');
            likeButton.classList.add('active');
            dislikeButton.classList.remove('active');
        } else if (data.user_reaction === false) {
            // Usuario dio dislike
            likeIcon.textContent = '🖒';
            dislikeIcon.textContent = '👎';
            likeButton.setAttribute('data-liked', 'false');
            likeButton.setAttribute('aria-pressed', 'false');
            dislikeButton.setAttribute('data-disliked', 'true');
            dislikeButton.setAttribute('aria-pressed', 'true');
            likeButton.classList.remove('active');
            dislikeButton.classList.add('active');
        } else {
            // Sin reacción
            likeIcon.textContent = '🖒';
            dislikeIcon.textContent = '👎︎';
            likeButton.setAttribute('data-liked', 'false');
            likeButton.setAttribute('aria-pressed', 'false');
            dislikeButton.setAttribute('data-disliked', 'false');
            dislikeButton.setAttribute('aria-pressed', 'false');
            likeButton.classList.remove('active');
            dislikeButton.classList.remove('active');
        }
    }

    function sendLikeRequest(pictureUuid, action) {
        console.log('Sending request:', { pictureUuid, action, csrfToken });
        
        fetch('/pages/picture/like_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                picture_uuid: pictureUuid,
                action: action,
                csrf_token: csrfToken
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            // Verificar si la respuesta es exitosa
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            // Verificar el content-type
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // Si no es JSON, obtener el texto para debug
                return response.text().then(text => {
                    console.error('Response is not JSON:', text);
                    throw new Error('Server did not return JSON');
                });
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                updateLikeDislikeButtons(data);
                console.log(data.message);
            } else {
                console.error('Server error:', data.message);
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Network error:', error);
            alert('Network error: ' + error.message);
        });
    }

    if (likeButton) {
        likeButton.addEventListener("click", function (e) {
            e.preventDefault();
            const pictureUuid = this.getAttribute("data-picture-uuid");
            if (pictureUuid) {
                sendLikeRequest(pictureUuid, 'like');
            } else {
                console.error('Picture UUID not found');
            }
        });
    }

    if (dislikeButton) {
        dislikeButton.addEventListener("click", function (e) {
            e.preventDefault();
            const pictureUuid = this.getAttribute("data-picture-uuid");
            if (pictureUuid) {
                sendLikeRequest(pictureUuid, 'dislike');
            } else {
                console.error('Picture UUID not found');
            }
        });
    }
});
