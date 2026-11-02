async function uploadImage(formData, uploadUrl) {
    try {
        const response = await fetch(uploadUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: formData
        });

        if (!response.ok) {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `エラーが発生しました: ${response.status}`);
            } else {
                const text = await response.text();
                throw new Error(`エラーが発生しました: ${response.status} - ${text.substring(0, 100)}`);
            }
        }

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('JSONの解析に失敗しました: レスポンスがJSON形式ではありません');
        }

        const data = await response.json();
        return data;
    } catch (error) {
        if (error instanceof SyntaxError) {
            throw new Error('JSONの解析に失敗しました: ' + error.message);
        }
        throw error;
    }
}






