@php
    $statePath = $getStatePath();
    $isDisabled = $isDisabled();
    $acceptedTypes = implode(',', $getAcceptedMimeTypes());
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @unless ($getUsesDirectS3Upload())
        <x-filament::section>
            <p class="text-sm text-danger-600 dark:text-danger-400">
                الرفع المباشر إلى S3 يتطلب ضبط <code class="text-xs">STORAGE_TYPE=s3</code> في ملف البيئة.
            </p>
        </x-filament::section>
    @else
        <div
            wire:ignore
            x-data="{
                path: $wire.$entangle('{{ $statePath }}'),
                uploading: false,
                progress: 0,
                error: null,
                fileName: null,
                presignUrl: @js($getPresignUrl()),
                confirmUrl: @js($getConfirmUrl()),
                maxSizeBytes: @js($getMaxSizeBytes()),
                acceptedTypes: @js($getAcceptedMimeTypes()),
                csrf: @js(csrf_token()),
                init() {
                    this.syncFileName(this.path);
                    this.$watch('path', (value) => this.syncFileName(value));
                },
                syncFileName(value) {
                    this.fileName = value ? value.split('/').pop() : null;
                },
                validateFile(file) {
                    this.error = null;

                    if (! file) {
                        return false;
                    }

                    if (! this.acceptedTypes.includes(file.type)) {
                        this.error = 'نوع الملف غير مدعوم. استخدم mp4 أو webm أو mov.';
                        return false;
                    }

                    if (file.size > this.maxSizeBytes) {
                        this.error = 'حجم الملف أكبر من الحد المسموح (500 ميجابايت).';
                        return false;
                    }

                    return true;
                },
                async upload(event) {
                    const file = event.target.files?.[0];

                    if (! this.validateFile(file)) {
                        event.target.value = '';
                        return;
                    }

                    this.uploading = true;
                    this.progress = 0;
                    this.error = null;

                    try {
                        const presignResponse = await fetch(this.presignUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrf,
                            },
                            body: JSON.stringify({
                                filename: file.name,
                                content_type: file.type,
                                size: file.size,
                            }),
                        });

                        const presign = await presignResponse.json();

                        if (! presignResponse.ok || ! presign.success) {
                            throw new Error(presign.message || 'تعذّر تجهيز رابط الرفع.');
                        }

                        await new Promise((resolve, reject) => {
                            const xhr = new XMLHttpRequest();

                            xhr.upload.addEventListener('progress', (progressEvent) => {
                                if (progressEvent.lengthComputable) {
                                    this.progress = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                                }
                            });

                            xhr.addEventListener('load', () => {
                                if (xhr.status >= 200 && xhr.status < 300) {
                                    resolve();
                                    return;
                                }

                                reject(new Error('فشل رفع الملف إلى S3.'));
                            });

                            xhr.addEventListener('error', () => reject(new Error('فشل الرفع إلى S3. غالباً بسبب إعداد CORS على الـ bucket. على السيرفر شغّل: php artisan s3:configure-upload-cors')));
                            xhr.addEventListener('abort', () => reject(new Error('تم إلغاء الرفع.')));

                            xhr.open('PUT', presign.upload_url);
                            xhr.setRequestHeader('Content-Type', presign.headers['Content-Type'] || file.type);
                            xhr.send(file);
                        });

                        const confirmResponse = await fetch(this.confirmUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrf,
                            },
                            body: JSON.stringify({ path: presign.path }),
                        });

                        const confirmed = await confirmResponse.json();

                        if (! confirmResponse.ok || ! confirmed.success) {
                            throw new Error(confirmed.message || 'تعذّر التحقق من الملف بعد الرفع.');
                        }

                        this.path = presign.path;
                        this.progress = 100;
                    } catch (exception) {
                        this.error = exception.message || 'حدث خطأ أثناء رفع الفيديو.';
                        this.path = null;
                        this.progress = 0;
                    } finally {
                        this.uploading = false;
                        event.target.value = '';
                    }
                },
                remove() {
                    this.path = null;
                    this.progress = 0;
                    this.error = null;
                    this.$refs.fileInput.value = '';
                },
            }"
            class="space-y-3"
        >
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                <template x-if="path && !uploading">
                    <div class="mb-3 flex items-center justify-between gap-3 rounded-lg bg-success-50 px-3 py-2 text-sm text-success-700 dark:bg-success-500/10 dark:text-success-400">
                        <span class="truncate" x-text="fileName"></span>
                        <button
                            type="button"
                            class="text-xs font-medium text-danger-600 hover:underline disabled:opacity-50 dark:text-danger-400"
                            x-on:click="remove()"
                            x-bind:disabled="@js($isDisabled) || uploading"
                        >
                            إزالة
                        </button>
                    </div>
                </template>

                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">
                        <span x-show="!path">اختر ملف فيديو</span>
                        <span x-show="path">استبدال الفيديو</span>
                    </span>
                    <input
                        type="file"
                        x-ref="fileInput"
                        accept="{{ $acceptedTypes }}"
                        class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-700 file:me-4 file:rounded-md file:border-0 file:bg-primary-600 file:px-4 file:py-2 file:font-medium file:text-white hover:file:bg-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                        x-on:change="upload($event)"
                        x-bind:disabled="@js($isDisabled) || uploading"
                    >
                </label>

                <div x-show="uploading" x-cloak class="mt-3 space-y-2">
                    <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                        <span>جاري الرفع مباشرة إلى S3...</span>
                        <span x-text="`${progress}%`"></span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div
                            class="h-full rounded-full bg-primary-600 transition-all duration-200"
                            x-bind:style="`width: ${progress}%`"
                        ></div>
                    </div>
                </div>

                <p
                    x-show="error"
                    x-cloak
                    x-text="error"
                    class="mt-3 text-sm text-danger-600 dark:text-danger-400"
                ></p>
            </div>
        </div>
    @endunless
</x-dynamic-component>
