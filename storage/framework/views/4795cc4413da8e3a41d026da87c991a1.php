
<?php if(str_ends_with($invitation->slug, '-preview')): ?>
<div class="preview-cta-wrapper">
    <a href="<?php echo e(route('register')); ?>" class="preview-cta-button">
        ✨ Gunakan Template Ini
    </a>
</div>

<style>
.preview-cta-wrapper {
    position: fixed;
    bottom: 80px; /* Di atas bottom navbar (navbar biasanya 60-70px) */
    left: 50%;
    transform: translateX(-50%);
    z-index: 998; /* Di bawah navbar (999) tapi di atas konten */
    width: 100%;
    max-width: 400px;
    padding: 0 20px;
    pointer-events: none; /* Agar tidak menghalangi klik di area sekitar */
}

.preview-cta-button {
    display: block;
    width: 100%;
    padding: 16px 32px;
    background: linear-gradient(135deg, #c9a96e 0%, #e8d5b0 100%);
    color: #1a1a1a;
    text-align: center;
    text-decoration: none;
    font-weight: 700;
    font-size: 16px;
    border-radius: 50px;
    box-shadow: 0 8px 24px rgba(201, 169, 110, 0.4);
    transition: all 0.3s ease;
    pointer-events: auto; /* Button bisa diklik */
    letter-spacing: 0.5px;
}

.preview-cta-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(201, 169, 110, 0.5);
    background: linear-gradient(135deg, #e8d5b0 0%, #c9a96e 100%);
}

.preview-cta-button:active {
    transform: translateY(0);
    box-shadow: 0 4px 16px rgba(201, 169, 110, 0.3);
}

/* Responsive */
@media (max-width: 480px) {
    .preview-cta-wrapper {
        bottom: 70px;
        max-width: calc(100% - 32px);
        padding: 0 16px;
    }
    
    .preview-cta-button {
        padding: 14px 24px;
        font-size: 15px;
    }
}

/* Animation on load */
@keyframes slideUpFade {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.preview-cta-button {
    animation: slideUpFade 0.5s ease-out 0.5s both;
}
</style>
<?php endif; ?>
<?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/invitation-templates/_cta_preview.blade.php ENDPATH**/ ?>