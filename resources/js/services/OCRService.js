// OCR Service for Meter Reading Detection
class OCRService {
    constructor() {
        this.worker = null;
        this.isInitialized = false;
        this.meterPatterns = [
            // Common water meter number patterns
            /mtr[\s\-]?(\d+)/i,
            /meter[\s\-]?(\d+)/i,
            /water[\s\-]?meter[\s\-]?(\d+)/i,
            /nyawasco[\s\-]?(\d+)/i,
            /[\s\-\_\|](\d{5,10})[\s\-\_\|]/,
            /^(\d{5,10})$/
        ];
    }

    async initialize() {
        if (this.isInitialized) return;

        try {
            this.worker = await Tesseract.createWorker();
            await this.worker.loadLanguage('eng');
            await this.worker.initialize('eng');
            
            // Configure for better meter reading detection
            await this.worker.setParameters({
                tessedit_char_whitelist: '0123456789MTRWATERNYWSCmeter ',
                tessedit_pageseg_mode: Tesseract.PSM.SINGLE_BLOCK,
                tessedit_ocr_engine_mode: Tesseract.OEM.LSTM_ONLY,
            });
            
            this.isInitialized = true;
            console.log('OCR Service initialized');
        } catch (error) {
            console.error('OCR initialization failed:', error);
            throw error;
        }
    }

    async detectMeter(imageElement) {
        if (!this.isInitialized) {
            await this.initialize();
        }

        try {
            const { data: { text, confidence } } = await this.worker.recognize(imageElement);
            
            console.log('OCR Raw Result:', { text, confidence });
            
            // Check if this looks like a water meter
            const meterInfo = this.validateMeterImage(text, confidence);
            
            if (meterInfo.isValidMeter) {
                const reading = this.extractMeterReading(text);
                return {
                    success: true,
                    isMeter: true,
                    meterNumber: meterInfo.meterNumber,
                    reading: reading,
                    confidence: confidence,
                    rawText: text
                };
            } else {
                return {
                    success: false,
                    isMeter: false,
                    message: 'This does not appear to be a valid water meter. Please capture a clear photo of the meter display.',
                    rawText: text
                };
            }
        } catch (error) {
            console.error('OCR processing error:', error);
            return {
                success: false,
                isMeter: false,
                message: 'Error processing image. Please try again.',
                error: error.message
            };
        }
    }

    validateMeterImage(text, confidence) {
        const cleanText = text.replace(/\s+/g, ' ').trim().toUpperCase();
        
        // Check for meter identifiers
        const hasMeterKeywords = /(MTR|METER|WATER|NYAWASCO|WSC)/i.test(cleanText);
        const hasSerialNumber = /[A-Z0-9]{5,15}/.test(cleanText);
        const hasReasonableConfidence = confidence > 50;

        // Extract meter number if present
        let meterNumber = null;
        for (const pattern of this.meterPatterns) {
            const match = cleanText.match(pattern);
            if (match && match[1]) {
                meterNumber = match[1];
                break;
            }
        }

        // Additional validation for meter numbers
        if (meterNumber) {
            // Validate meter number format (typically 5-10 digits)
            const isValidFormat = /^\d{5,10}$/.test(meterNumber);
            if (!isValidFormat) {
                meterNumber = null;
            }
        }

        return {
            isValidMeter: (hasMeterKeywords || hasSerialNumber) && hasReasonableConfidence,
            meterNumber: meterNumber,
            confidence: confidence,
            hasKeywords: hasMeterKeywords,
            hasSerial: hasSerialNumber
        };
    }

    extractMeterReading(text) {
        const cleanText = text.replace(/\s+/g, '').replace(/[^\d.]/g, '');
        
        if (!cleanText) return null;

        // Find all number sequences
        const numberMatches = cleanText.match(/\d+\.?\d*/g);
        
        if (!numberMatches || numberMatches.length === 0) {
            return null;
        }

        // Filter for reasonable meter readings
        const validReadings = numberMatches.filter(num => {
            const value = parseFloat(num);
            // Meter readings are typically positive numbers
            return !isNaN(value) && value > 0 && value <= 99999999;
        });

        if (validReadings.length === 0) {
            return null;
        }

        // For meter readings, we typically want numbers that look like consumption
        // (often the largest number that's not too large)
        const readings = validReadings.map(num => parseFloat(num));
        const maxReading = Math.max(...readings);
        
        // If there's a number that's significantly larger than others, it's likely the meter reading
        const otherReadings = readings.filter(r => r < maxReading * 0.8);
        const finalReading = otherReadings.length > 0 ? maxReading : readings[0];

        return finalReading.toFixed(2);
    }

    async terminate() {
        if (this.worker) {
            await this.worker.terminate();
            this.isInitialized = false;
            this.worker = null;
        }
    }
}

// Create global instance
window.OCRService = new OCRService();