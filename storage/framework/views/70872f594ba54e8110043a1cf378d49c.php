
<?php if(isset($invitation) && $invitation->isGiftActive() && $invitation->bankAccounts->count()): ?>
<?php $accounts = $invitation->bankAccounts; ?>

<section id="gift" class="section <?php echo e($giftSectionClass ?? ''); ?> reveal">
    <p class="t-upper t-muted">Hadiah Digital</p>
    <h2 class="section-title">Gift & Amplop Digital</h2>
    <div class="divider"></div>
    <p class="t-muted" style="max-width:480px;margin:0 auto 24px;font-size:.9rem;line-height:1.7">
        Doa restu Anda adalah hadiah terbaik bagi kami. Namun jika ingin memberikan hadiah, silakan pilih rekening di bawah.
    </p>

    
    <?php if($accounts->count() > 1): ?>
    <div style="max-width:320px;margin:0 auto 24px">
        <select id="bankSelector" class="inv-bank-selector" onchange="selectBank(this.value)">
            <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($i); ?>"><?php echo e($account->bank_name); ?> — <?php echo e($account->account_name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <?php endif; ?>

    
    <div class="bank-cards-wrap">
        <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="inv-bank-card <?php echo e($i > 0 ? 'bank-card-hidden' : ''); ?>"
             id="bankCard<?php echo e($i); ?>"
             style="background: <?php echo e($account->bankColor()); ?>">
            <div class="inv-bank-header">
                <div class="inv-bank-initial"><?php echo e($account->bankInitial()); ?></div>
                <div class="inv-bank-name"><?php echo e(strtoupper($account->bank_name)); ?></div>
            </div>
            <div class="inv-bank-number">
                <?php echo e(chunk_split($account->account_number, 4, ' ')); ?>

            </div>
            <div class="inv-bank-footer">
                <div>
                    <div class="inv-bank-label">Atas Nama</div>
                    <div class="inv-bank-owner"><?php echo e($account->account_name); ?></div>
                </div>
                <button class="inv-copy-btn"
                    onclick="copyToClipboard('<?php echo e($account->account_number); ?>', this)"
                    title="Salin nomor rekening">
                    📋
                </button>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>

<style>
.inv-bank-selector {
    width: 100%;
    padding: 10px 14px;
    border-radius: 10px;
    border: 1px solid rgba(0,0,0,.12);
    background: #fff;
    font-size: .9rem;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23666' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 36px;
}

.bank-card-hidden { display: none !important; }
</style>

<script>
function selectBank(idx) {
    document.querySelectorAll('[id^="bankCard"]').forEach((el, i) => {
        el.classList.toggle('bank-card-hidden', i !== parseInt(idx));
    });
}
</script>
<?php endif; ?>
<?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/invitation-templates/_gift.blade.php ENDPATH**/ ?>