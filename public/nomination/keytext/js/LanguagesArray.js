//Version 1.0.0.3
var languageSupport = new Array("hindi", "urdu", "marathi", "gujarati", "bengali", "punjabi", "malayalam", "kannada", "assamese", "bodo", "dogri", "kashmiridev", "konkani", "maithili", "manipuri", "meeteimayek", "nepali", "olchiki", "oriya", "sanskrit", "santali", "sindhidev", "tamil", "telugu"); //Language which are supported by keyboard must be entered here
var languageLocaleSupport = new Array("hin", "urd", "mar", "guj", "ben", "pan", "mal", "kan", "asm", "brx", "dogri", "kashmiridev", "knn", "mai", "mni", "meeteimayek", "nep", "olchiki", "ory", "sanskrit", "sat", "snd", "tam", "tel"); //Language locale which are supported by keyboard must be entered here
var asciiArray = new Array("`", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "-", "=", "q", "w", "e", "r", "t", "y", "u", "i", "o", "p", "[", "]", "\\", "a", "s", "d", "f", "g", "h", "j", "k", "l", ";", "'", "z", "x", "c", "v", "b", "n", "m", ",", ".", "/", "~", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "+", "Q", "W", "E", "R", "T", "Y", "U", "I", "O", "P", "{", "}", "|", "A", "S", "D", "F", "G", "H", "J", "K", "L", ":", "\" ", "Z", "X", "C", "V", "B", "N", "M", "<", ">", "?");

//***************************Hindi
var mapHindiArray = new Array(" ", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "-", "ृ", "ौ", "ै", "ा", "ी", "ू", "ब", "ह", "ग", "द", "ज", "ड", "़", "ॉ", "ो", "े", "्", "ि", "ु", "प", "र", "क", "त", "च", "ट", " ", "ं", "म", "न", "व", "ल", "स", ",", ".", "य");
var mapHindiShiftArray = new Array(" ", "ऍ", "ॅ", "्र", "र्", "ज्ञ", "त्र", "क्ष", "श्र", "(", ")", "ः", "ऋ", "औ", "ऐ", "आ", "ई", "ऊ", "भ", "ङ", "घ", "ध", "झ", "ढ", "ञ", "ऑ", "ओ", "ए", "अ", "इ", "उ", "फ", " ", "ख", "थ", "छ", "ठ", " ", "ँ", "ण", " ", " ", " ", "श", "ष", "।", " ");
var mapHindiExtArray = new Array(" ", "", "", " ", "₹", " ", " ", " ", " ", " ", " ", " ", "ॄ", " ", " ", "॑", "ॣ", " ", " ", " ", "ग़", " ", "ज़", "ड़", " ", " ", " ", " ", "॒", "ॢ", " ", " ", " ", "क़", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "॰", "॥", " ");
var mapHindiShiftExtArray = new Array(" ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "ॠ", " ", " ", " ", "ॡ", " ", " ", " ", " ", " ", " ", "ढ़", " ", " ", " ", " ", " ", "ऌ", " ", "फ़", " ", "ख़", " ", " ", " ", " ", "ॐ", " ", " ", " ", "ळ", " ", " ", "ऽ", " ");
//***************************Hindi Array Ends Here

//***************************Urdu Enhance Inscript
var mapUrduArray = new Array(" ", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "۰", "-", "=", "ق", "ئ", "ے", "ھ", "گ", "ج", "ش", "ف", "چ", "ح", "[", "]", "\\", "آ", "ۂ", "ی", "ن", "ر", "و", "ا", "ک", "ل", "؛", "'", "ت", "پ", "ہ", "د", "ب", "س", "م", "،", "۔", "/");
var mapUrduShiftArray = new Array("ُ", "!", "@", "ِ", "ّ", "٪", "ٗ", "ٔ", "َ", "(", ")", "ـ", "ٕ", "إ", "ؤ", "ۓ", "ﷲ", "غ", "ى", "ض", "ژ", "ۀ", " ", "{", "}", "|", "أ", "ۃ", "ء", "ں", "ز", "ڈ", "ع", "خ", "ط", ":", "\" ", "ٹ", "ذ", " ", "ڑ", "ث", "ص", "ظ", "<", ">", "؟");
var mapUrduExtArray = new Array("ٖ", "", "", "ْ", "₹", "ٓ", "ً", "ٌ", " ", "ٰ", "ٍ", "؂", " ", " ", " ", "ؑ", "ؔ", "ؓ", "ؐ", "ؒ", " ", " ", " ", "﴾", "﴿", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "٬", " ", " ", " ", " ", "؃", "؁", " ﷺ", " ", "؀", "؞", "؍");
var mapUrduShiftExtArray = new Array(" ", " ", " ", " ", " ", " ", " ", " ", "٭", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "۞", "۝", " ");
//***************************Urdu Enhance Array Ends Here

//***************************Marathi
var mapMarathiArray = new Array(" ", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "-", "ृ", "ौ", "ै", "ा", "ी", "ू", "ब", "ह", "ग", "द", "ज", "ड", "़", "ॉ", "ो", "े", "्", "ि", "ु", "प", "र", "क", "त", "च", "ट", " ", "ं", "म", "न", "व", "ल", "स", ",", ".", "य");
var mapMarathiShiftArray = new Array(" ", "ॲ", "ॅ", "्र", "र्", "ज्ञ", "त्र", "क्ष", "श्र", "(", ")", "ः", "ऋ", "औ", "ऐ", "आ", "ई", "ऊ", "भ", "ङ", "घ", "ध", "झ", "ढ", "ञ", "ऑ", "ओ", "ए", "अ", "इ", "उ", "फ", "ऱ", "ख", "थ", "छ", "ठ", " ", "ँ", "ण", " ", " ", "ळ", "श", "ष", "।", " ");
var mapMarathiExtArray = new Array(" ", "", "", " ", "₹", " ", " ", " ", " ", " ", " ", " ", "ॄ", " ", " ", "॑", "ॣ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "॒", "ॢ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "॰", "॥", " ");
var mapMarathiShiftExtArray = new Array(" ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "ॠ", " ", " ", " ", "ॡ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "ऌ", " ", " ", " ", " ", " ", " ", " ", " ", "ॐ", " ", " ", " ", " ", " ", " ", "ऽ", " ");
//***************************Marathi Array Ends Here

//***************************Gujrati
var mapGujaratiArray = new Array("‎", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "-", "ૃ", "ૌ", "ૈ", "ા", "ી", "ૂ", "બ", "હ", "ગ", "દ", "જ", "ડ", "઼", "ૉ", "ો", "ે", "્", "િ", "ુ", "પ", "ર", "ક", "ત", "ચ", "ટ", "‎", "ં", "મ", "ન", "વ", "લ", "સ", ",", ".", "ય");
var mapGujaratiShiftArray = new Array("‎", "ઍ", "ૅ", "્ર", "ર્", "જ્ઞ", "ત્ર", "ક્ષ", "શ્ર", "(", ")", "ઃ", "ઋ", "ઔ", "ઐ", "આ", "ઈ", "ઊ", "ભ", "ઙ", "ઘ", "ધ", "ઝ", "ઢ", "ઞ", "ઑ", "ઓ", "એ", "અ", "ઇ", "ઉ", "ફ", "‎", "ખ", "થ", "છ", "ઠ", "‎", "ઁ", "ણ", "‎", "‎", "ળ", "શ", "ષ", "।", "‎");
var mapGujaratiExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ૄ", "‎", "‎", "‎", "ૣ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ૢ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "૱", "॥", "‎");
var mapGujaratiShiftExtArray = new Array("‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ૠ", "‎", "‎", "‎", "ૡ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ઌ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ૐ", "‎", "‎", "‎", "‎", "‎", "‎", "ઽ", "‎");
//***************************Gujrati Array Ends Here

//***************************Bengali
var mapBengaliArray = new Array(" ", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "-", "ৃ", "ৌ", "ৈ", "া", "ী", "ূ", "ব", "হ", "গ", "দ", "জ", "ড", "়", " ", "ো", "ে", "্", "ি", "ু", "প", "র", "ক", "ত", "চ", "ট", "ʼ", "ং", "ম", "ন", " ", "ল", "স", ",", ".", "য়");
var mapBengaliShiftArray = new Array(" ", "অ্যা", " ", "্র", "র্", "জ্ঞ", "ত্র", "ক্ষ", "শ্র", "( ", ") ", "ঃ", "ঋ", "ঔ", "ঐ", "আ", "ঈ", "ঊ", "ভ", "ঙ", "ঘ", "ধ", "ঝ", "ঢ", "ঞ", " ", "ও", "এ", "অ", "ই", "উ", "ফ", " ", "খ", "থ", "ছ", "ঠ", " ", "ঁ", "ণ", " ", " ", " ", "শ", "ষ", "।", "য");
var mapBengaliExtArray = new Array(" ", "", "", " ", "₹", " ", " ", " ", " ", " ", " ", " ", "ৄ", " ", " ", " ", "ৣ", " ", " ", " ", " ", " ", " ", "ড়", " ", " ", " ", " ", " ", "ৢ", " ", " ", " ", " ", "ৎ", " ", " ", " ", "৺", " ", " ", " ", " ", " ", "৳", "॥", "্য");
var mapBengaliShiftExtArray = new Array(" ", "৴", "৵", "৶", "৷", "৸", "৹", " ", " ", " ", " ", " ", "ৠ", " ", " ", " ", "ৡ", " ", " ", " ", " ", " ", " ", "ঢ়", " ", " ", " ", " ", " ", "ঌ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "৲", "ঽ", "৻");
//***************************Bengali Array Ends Here

//***************************Punjabi
var mapPunjabiArray = new Array(" ", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "-", " ", "ੌ", "ੈ", "ਾ", "ੀ", "ੂ", "ਬ", "ਹ", "ਗ", "ਦ", "ਜ", "ਡ", "਼", " ", "ੋ", "ੇ", "੍", "ਿ", "ੁ", "ਪ", "ਰ", "ਕ", "ਤ", "ਚ", "ਟ", " ", "ੰ", "ਮ", "ਨ", "ਵ", "ਲ", "ਸ", " ,", ".", "ਯ");
var mapPunjabiShiftArray = new Array(" ", " ", " ", " ", "ੱ", " ", " ", " ", " ", "( ", ") ", "ਃ", " ", "ਔ", "ਐ", "ਆ", "ਈ", "ਊ", "ਭ", "ਙ", "ਘ", "ਧ", "ਝ", "ਢ", "ਞ", " ", "ਓ", "ਏ", "ਅ", "ਇ", "ਉ", "ਫ", " ", "ਖ", "ਥ", "ਛ", "ਠ", " ", "ਂ", "ਣ", " ", " ", " ", "ਸ਼", " ", "।", " ");
var mapPunjabiExtArray = new Array(" ", "", "", " ", "₹", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "ਗ਼", " ", "ਜ਼", "ੜ", " ", " ", " ", " ", "ੑ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "ਁ", " ", " ", " ", " ", " ", " ", "॥", "ੵ");
var mapPunjabiShiftExtArray = new Array(" ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "☬", "ੲ", "ੳ", "ਫ਼", " ", "ਖ਼", " ", " ", " ", " ", "ੴ", " ", " ", " ", "ਲ਼", " ", " ", " ", " ");
//***************************Punjabi Array Ends Here

//***************************Malayalam
var mapMalayalamArray = new Array("ൊ", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "-", "ൃ", "ൗ", "ൈ", "ാ", "ീ", "ൂ", "ബ", "ഹ", "ഗ", "ദ", "ജ", "ഡ", " ", "ർ", "ോ", "േ", "്", "ി", "ു", "പ", "ര", "ക", "ത", "ച", "ട", "െ", "ം", "മ", "ന", "വ", "ല", "സ", ",", ".", "യ");
var mapMalayalamShiftArray = new Array("ഒ", " ", " ", "്ര", " ", " ", " ", "ക്ഷ", "ൾ", " ", " ", "ഃ", "ഋ", "ഔ", "ഐ", "ആ", "ഈ", "ഊ", "ഭ", "ങ", "ഘ", "ധ", "ഝ", "ഢ", "ഞ", " ", "ഓ", "ഏ", "അ", "ഇ", "ഉ", "ഫ", "റ", "ഖ", "ഥ", "ഛ", "ഠ", "എ", "ൺ", "ണ", "ൻ", "ഴ", "ള", "ശ", "ഷ", "ൽ", " ");
var mapMalayalamExtArray = new Array(" ", "", "", " ", "₹", " ", " ", " ", " ", " ", " ", " ", "ൄ", "ൌ", " ", " ", "ൣ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "ൢ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ");
var mapMalayalamShiftExtArray = new Array(" ", "൰", "൱", "൲", "൳", "൴", "൵", " ", " ", " ", " ", " ", "ൠ", " ", " ", " ", "ൡ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "ഌ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "൹", " ", " ", " ", " ", "ഽ", " ");
//***************************Malayalam Array Ends Here

//***************************Kannada
var mapKannadaArray = new Array("ೊ", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", " ", "ೃ", "ೌ", "ೈ", "ಾ", "ೀ", "ೂ", "ಬ", "ಹ", "ಗ", "ದ", "ಜ", "ಡ", "಼", " ", "ೋ", "ೇ", "್", "ಿ", "ು", "ಪ", "ರ", "ಕ", "ತ", "ಚ", "ಟ", "ೆ", "ಂ", "ಮ", "ನ", "ವ", "ಲ", "ಸ", ",", ".", "ಯ");
var mapKannadaShiftArray = new Array("ಒ", " ", " ", "್ರ", " ", "ಜ್ಞ", "ತ್ರ", "ಕ್ಷ", "ಶ್ರ", " ", " ", "ಃ", "ಋ", "ಔ", "ಐ", "ಆ", "ಈ", "ಊ", "ಭ", "ಙ", "ಘ", "ಧ", "ಝ", "ಢ", "ಞ", " ", "ಓ", "ಏ", "ಅ", "ಇ", "ಉ", "ಫ", "ಱ", "ಖ", "ಥ", "ಛ", "ಠ", "ಎ", " ", "ಣ", " ", " ", "ಳ", "ಶ", "ಷ", "|", " ");
var mapKannadaExtArray = new Array(" ", "", "", " ", "₹", " ", " ", " ", " ", " ", " ", " ", "ೄ", " ", " ", " ", "ೣ", " ", " ", "ೱ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "ೢ", " ", " ", "ೲ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "॥", " ");
var mapKannadaShiftExtArray = new Array(" ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "ೠ", " ", " ", " ", "ೡ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "ಌ", " ", "ೞ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "ಽ", " ");
//***************************Kannada Array Ends Here

//***************************Assamese
var mapAssameseArray = new Array("‎", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "ৃ", "ৌ", "ৈ", "া", "ী", "ূ", "ব", "হ", "গ", "দ", "জ", "ড", "়", "‎", "ো", "ে", "্", "ি", "ু", "প", "ৰ", "ক", "ত", "চ", "ট", "ʼ", "ং", "ম", "ন", "ৱ", "ল", "স", ",‎", ".‎", "য়");
var mapAssameseShiftArray = new Array("‎", "অ্যা", "‎", "্ৰ", "ৰ্", "জ্ঞ", "ত", "ক্ষ", "শ্ৰ", "‎", "‎", "ঃ", "ঋ", "ঔ", "ঐ", "আ", "ঈ", "ঊ", "ভ", "ঙ", "ঘ", "ধ", "ঝ", "ঢ", "ঞ", "‎", "ও", "এ", "অ", "ই", "উ", "ফ", "‎", "খ", "থ", "ছ", "ঠ", "‎", "ঁ", "ণ", "‎", "‎", "‎", "শ", "ষ", "।", "য");
var mapAssameseExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ৄ", "‎", "‎", "‎", "ৣ", "‎", "‎", "‎", "‎", "‎", "‎", "ড়", "‎", "‎", "‎", "‎", "‎", "ৢ", "‎", "‎", "‎", "‎", "ৎ", "‎", "‎", "‎", "৺", "‎", "‎", "‎", "‎", "‎", "৳", "॥", "্য");
var mapAssameseShiftExtArray = new Array("‎", "৴", "৵", "৶", "৷", "৸", "৹", "‎", "‎", "‎", "‎", "‎", "ৠ", "‎", "‎", "‎", "ৡ", "‎", "‎", "‎", "‎", "‎", "‎", "ঢ়", "‎", "‎", "‎", "‎", "‎", "ঌ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "৲", "ঽ", "‎");
//***************************Assamese Array Ends Here

//***************************Bodo
var mapBodoArray = new Array("ॊ", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "ृ", "ौ", "ै", "ा", "ी", "ू", "ब", "ह", "ग", "द", "ज", "ड", "़", "‎", "ो", "े", "्", "ि", "ु", "प", "र", "क", "त", "च", "ट", "ʼ", "ं", "म", "न", "व", "ल", "स", ",‎", ".‎", "य");
var mapBodoShiftArray = new Array("ऒ", "‎", "‎", "्र", "र्", "ज्ञ", "त्र", "क्ष", "श्र", "‎", "‎", "ः", "ऋ", "औ", "ऐ", "आ", "ई", "ऊ", "भ", "ङ", "घ", "ध", "झ", "ढ", "ञ", "‎", "ओ", "ए", "अ", "इ", "उ", "फ", "‎", "ख", "थ", "छ", "ठ", "‎", "ँ", "ण", "‎", "‎", "‎", "श", "ष", "।", "‎");
var mapBodoExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॄ", "‎", "‎", "‎", "ॣ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॢ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "॰", "॥", "‎");
var mapBodoShiftExtArray = new Array("‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॠ", "‎", "‎", "‎", "ॡ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ऌ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॐ", "‎", "‎", "‎", "ळ", "‎", "‎", "ऽ", "‎");
//***************************Bodo Array Ends Here

//***************************Dogri
var mapDogriArray = new Array("‎", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "ृ", "ौ", "ै", "ा", "ी", "ू", "ब", "ह", "ग", "द", "ज", "ड", "़", "‎", "ो", "े", "्", "ि", "ु", "प", "र", "क", "त", "च", "ट", "ʼ", "ं", "म", "न", "व", "ल", "स", ",‎", ".‎", "य");
var mapDogriShiftArray = new Array("‎", "‎", "‎", "्र", "र्", "ज्ञ", "त्र", "क्ष", "श्र", "‎", "‎", "ः", "ऋ", "औ", "ऐ", "आ", "ई", "ऊ", "भ", "ङ", "घ", "ध", "झ", "ढ", "ञ", "‎", "ओ", "ए", "अ", "इ", "उ", "फ", "‎", "ख", "थ", "छ", "ठ", "‎", "‎", "ण", "‎", "‎", "‎", "श", "ष", "।", "‎");
var mapDogriExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॄ", "‎", "‎", "‎", "ॣ", "‎", "‎", "‎", "‎", "‎", "ज़", "ड़", "‎", "‎", "‎", "‎", "‎", "ॢ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "॥", "‎");
var mapDogriShiftExtArray = new Array("‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॠ", "‎", "‎", "‎", "ॡ", "‎", "‎", "‎", "‎", "‎", "‎", "ढ़", "‎", "‎", "‎", "‎", "‎", "ऌ", "‎", "फ़", "‎", "‎", "‎", "‎", "‎", "‎", "ॐ", "‎", "‎", "‎", "‎", "‎", "‎", "ऽ", "‎");
//***************************Dogri Array Ends Here

//***************************KashmiriDev
var mapKashmiriDevArray = new Array("ॊ", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "ृ", "ौ", "ै", "ा", "ी", "ू", "ब", "ह", "ग", "द", "ज", "ड", "़", "ॉ", "ो", "े", "्", "ि", "ु", "प", "र", "क", "त", "च", "ट", "ॆ", "ं", "म", "न", "व", "ल", "स", "‎,", ".‎", "य");
var mapKashmiriDevShiftArray = new Array("ऒ", "ॲ", "ॅ", "्र", "र्", "ज्ञ", "त्र", "क्ष", "श्र", "‎", "‎", "ः", "ऋ", "औ", "ऐ", "आ", "ई", "ऊ", "भ", "ङ", "घ", "ध", "झ", "ढ", "ञ", "ऑ", "ओ", "ए", "अ", "इ", "उ", "फ", "‎", "ख", "थ", "छ", "ठ", "ऎ", "ँ", "ण", "‎", "‎", "‎", "श", "ष", "।", "‎");
var mapKashmiriDevExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॄ", "ॏ", "‎", "‎", "‎", "ॗ", "‎", "‎", "ग़", "‎", "ज़", "ड़", "‎", "‎", "ऻ", "‎", "‎", "‎", "ॖ", "‎", "‎", "क़", "‎", "‎", "‎", "ऺ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "॥", "‎");
var mapKashmiriDevShiftExtArray = new Array("‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॠ", "ॵ", "‎", "‎", "‎", "ॷ", "‎", "‎", "‎", "‎", "‎", "ढ़", "‎", "‎", "ॴ", "‎", "‎", "‎", "ॶ", "फ़", "‎", "ख़", "‎", "‎", "‎", "ॳ", "ॐ", "‎", "‎", "‎", "‎", "‎", "‎", "ऽ", "‎");
//***************************KashmiriDev Array Ends Here

//***************************Konkani
var mapKonkaniArray = new Array("‎", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "ृ", "ौ", "ै", "ा", "ी", "ू", "ब", "ह", "ग", "द", "ज", "ड", "़", "ॉ", "ो", "े", "्", "ि", "ु", "प", "र", "क", "त", "च", "ट", "‎", "ं", "म", "न", "व", "ल", "स", "‎,", "‎.", "य");
var mapKonkaniShiftArray = new Array("‎", "ॲ", "ॅ", "्र", "र्", "ज्ञ", "त्र", "क्ष", "श्र", "‎", "‎", "ः", "ऋ", "औ", "ऐ", "आ", "ई", "ऊ", "भ", "ङ", "घ", "ध", "झ", "ढ", "ञ", "ऑ", "ओ", "ए", "अ", "इ", "उ", "फ", "ऱ", "ख", "थ", "छ", "ठ", "‎", "ँ", "ण", "‎", "‎", "ळ", "श", "ष", "।", "‎");
var mapKonkaniExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॄ", "‎", "‎", "‎", "ॣ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॢ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "॰", "॥", "‎");
var mapKonkaniShiftExtArray = new Array("‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॠ", "‎", "‎", "‎", "ॡ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ऌ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॐ", "‎", "‎", "‎", "‎", "‎", "‎", "ऽ", "‎");
//***************************Konkani Array Ends Here

//***************************Maithili
var mapMaithiliArray = new Array("‎", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "ृ", "ौ", "ै", "ा", "ी", "ू", "ब", "ह", "ग", "द", "ज", "ड", "़", "‎", "ो", "े", "्", "ि", "ु", "प", "र", "क", "त", "च", "ट", "ʼ", "ं", "म", "न", "व", "ल", "स", "‎,", ".‎", "य");
var mapMaithiliShiftArray = new Array("‎", "‎", "‎", "्र", "र्", "ज्ञ", "त्र", "क्ष", "श्र", "‎", "‎", "ः", "ऋ", "औ", "ऐ", "आ", "ई", "ऊ", "भ", "ङ", "घ", "ध", "झ", "ढ", "ञ", "‎", "ओ", "ए", "अ", "इ", "उ", "फ", "‎", "ख", "थ", "छ", "ठ", "‎", "ँ", "ण", "‎", "‎", "‎", "श", "ष", "।", "‎");
var mapMaithiliExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॄ", "‎", "‎", "‎", "ॣ", "‎", "‎", "‎", "‎", "‎", "‎", "ड़", "‎", "‎", "‎", "‎", "‎", "ॢ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "॥", "‎");
var mapMaithiliShiftExtArray = new Array("‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॠ", "‎", "‎", "‎", "ॡ", "‎", "‎", "‎", "‎", "‎", "‎", "ढ़", "‎", "‎", "‎", "‎", "‎", "ऌ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॐ", "‎", "‎", "‎", "‎", "‎", "‎", "ऽ", "‎");
//***************************Maithili Array Ends Here

//***************************Manipuri
var mapManipuriArray = new Array("‎", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "ৃ", "ৌ", "ৈ", "া", "ী", "ূ", "ব", "হ", "গ", "দ", "জ", "ড", "়", "‎", "ো", "ে", "্", "ি", "ু", "প", "র", "ক", "ত", "চ", "ট", "‎", "ং", "ম", "ন", "ৱ", "ল", "স", "‎,", "‎.", "য়");
var mapManipuriShiftArray = new Array("‎", "অ্যা", "‎", "্র", "র্", "জ্ঞ", "ত্র", "ক্ষ", "শ্র", "‎", "‎", "ঃ", "ঋ", "ঔ", "ঐ", "আ", "ঈ", "ঊ", "ভ", "ঙ", "ঘ", "ধ", "ঝ", "ঢ", "ঞ", "‎", "ও", "এ", "অ", "ই", "উ", "ফ", "‎", "খ", "থ", "ছ", "ঠ", "‎", "ঁ", "ণ", "‎", "‎", "‎", "শ", "ষ", "।", "য");
var mapManipuriExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ৄ", "‎", "‎", "‎", "ৣ", "‎", "‎", "‎", "‎", "‎", "‎", "ড়", "‎", "‎", "‎", "‎", "‎", "ৢ", "‎", "‎", "‎", "‎", "ৎ", "‎", "‎", "‎", "৺", "‎", "‎", "‎", "‎", "‎", "৳", "॥", "্য");
var mapManipuriShiftExtArray = new Array("‎", "৴", "৵", "৶", "৷", "৸", "৹", "‎", "‎", "‎", "‎", "‎", "ৠ", "‎", "‎", "‎", "ৡ", "‎", "‎", "‎", "‎", "‎", "‎", "ঢ়", "‎", "‎", "‎", "‎", "‎", "ঌ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "৲", "ঽ", "‎");
//***************************Manipuri Array Ends Here

//***************************MeeteiMayek
var mapMeeteiMayekArray = new Array("‎", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "‎", "ꯧ", "ꯩ", "ꯥ", "‎", "‎", "ꯕ", "ꯍ", "ꯒ", "ꯗ", "ꯖ", "‎", "꯬", "‎", "ꯣ", "ꯦ", "꯭", "ꯤ", "ꯨ", "ꯄ", "ꯔ", "ꯀ", "ꯇ", "ꯆ", "‎", "‎", "ꯪ", "ꯃ", "ꯅ", "ꯋ", "ꯂ", "ꯁ", "‎,", "‎.", "ꯌ");
var mapMeeteiMayekShiftArray = new Array("‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ꯚ", "ꯉ", "ꯘ", "ꯙ", "ꯓ", "‎", "‎", "‎", "‎", "‎", "ꯑ", "ꯏ", "ꯎ", "ꯐ", "‎", "ꯈ", "ꯊ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "꯫", "‎");
var mapMeeteiMayekExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‍", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ꯞ", "‎", "ꯛ", "ꯠ", "‎", "‎", "‎", "‎", "ꯝ", "ꯟ", "‎", "ꯜ", "‎", "‎", "‎", "‎");
var mapMeeteiMayekShiftExtArray = new Array("‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ꯡ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ꯢ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎");
//***************************MeeteiMayek Array Ends Here

//***************************Nepali
var mapNepaliArray = new Array("‎", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "ृ", "ौ", "ै", "ा", "ी", "ू", "ब", "ह", "ग", "द", "ज", "ड", "़", "‎", "ो", "े", "्", "ि", "ु", "प", "र", "क", "त", "च", "ट", "‎", "ं", "म", "न", "व", "ल", "स", "‎,", "‎.", "य");
var mapNepaliShiftArray = new Array("‎", "‎", "‎", "्र", "र्", "ज्ञ", "त्र", "क्ष", "श्र", "‎", "‎", "ः", "ऋ", "औ", "ऐ", "आ", "ई", "ऊ", "भ", "ङ", "घ", "ध", "झ", "ढ", "ञ", "‎", "ओ", "ए", "अ", "इ", "उ", "फ", "ऱ", "ख", "थ", "छ", "ठ", "‎", "ँ", "ण", "‎", "‎", "ळ", "श", "ष", "।", "‎");
var mapNepaliExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॄ", "‎", "‎", "‎", "ॣ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॢ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॱ", "‎", "‎", "‎", "‎", "‎", "॰", "॥", "‎");
var mapNepaliShiftExtArray = new Array("‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॠ", "‎", "‎", "‎", "ॡ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ऌ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॐ", "‎", "‎", "‎", "‎", "‎", "‎", "ऽ", "‎");
//***************************Nepali Array Ends Here

//***************************OlChiki
var mapOlChikiArray = new Array("‎", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "‎", "‎", "‎", "ᱟ", "‎", "‎", "ᱵ", "ᱦ", "ᱜ", "ᱫ", "ᱡ", "ᱰ", "ᱹ", "‎", "ᱳ", "ᱮ", "ᱚ", "ᱤ", "ᱩ", "ᱯ", "ᱨ", "ᱠ", "ᱛ", "ᱪ", "ᱴ", "ᱷ", "ᱸ", "ᱢ", "ᱱ", "ᱣ", "ᱞ", "ᱥ", "‎,", "‎.", "ᱭ");
var mapOlChikiShiftArray = new Array("ᱻ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ᱼ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ᱝ", "‎", "‎", "‎", "ᱲ", "ᱧ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ᱺ", "‎", "ᱽ", "‎", "ᱬ", "‎", "ᱶ", "‎", "‎", "᱿", "᱾", "‎");
var mapOlChikiExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‍", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎");
var mapOlChikiShiftExtArray = new Array("‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎");
//***************************OlChiki Array Ends Here

//***************************Oriya
var mapOriyaArray = new Array("‎", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "ୃ", "ୌ", "ୈ", "ା", "ୀ", "ୂ", "ବ", "ହ", "ଗ", "ଦ", "ଜ", "ଡ", "଼", "‎", "ୋ", "େ", "୍", "ି", "ୁ", "ପ", "ର", "କ", "ତ", "ଚ", "ଟ", "‎", "ଂ", "ମ", "ନ", "ୱ", "ଲ", "ସ", "‎,", "‎.", "ୟ");
var mapOriyaShiftArray = new Array("‎", "‎", "‎", "୍ର", "ର୍", "ଜ୍ଞ", "ତ୍ର", "କ୍ଷ", "ଶ୍ର", "‎", "‎", "ଃ", "ଋ", "ଔ", "ଐ", "ଆ", "ଈ", "ଊ", "ଭ", "ଙ", "ଘ", "ଧ", "ଝ", "ଢ", "ଞ", "‎", "ଓ", "ଏ", "ଅ", "ଇ", "ଉ", "ଫ", "‎", "ଖ", "ଥ", "ଛ", "ଠ", "‎", "ଁ", "ଣ", "‎", "‎", "ଳ", "ଶ", "ଷ", "।", "ଯ");
var mapOriyaExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ୄ", "‎", "‎", "‎", "ୣ", "‎", "‎", "‎", "‎", "‎", "‎", "ଡ଼", "‎", "‎", "‎", "‎", "‎", "ୢ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "୰", "‎", "‎", "ଵ", "‎", "‎", "‎", "॥", "‎");
var mapOriyaShiftExtArray = new Array("‎", "‎", "‎", "‎", "୲", "୳", "୴", "୵", "୶", "୷", "‎", "‎", "ୠ", "‎", "‎", "‎", "ୡ", "‎", "‎", "‎", "‎", "‎", "‎", "ଢ଼", "‎", "‎", "‎", "‎", "‎", "ଌ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ଽ", "‎");
//***************************Oriya Array Ends Here

//***************************Sanskrit
var mapSanskritArray = new Array("‎", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "ृ", "ौ", "ै", "ा", "ी", "ू", "ब", "ह", "ग", "द", "ज", "ड", "़", "ॄ", "ो", "े", "्", "ि", "ु", "प", "र", "क", "त", "च", "ट", "‎", "ं", "म", "न", "व", "ल", "स", "‎,", ".‎", "य");
var mapSanskritShiftArray = new Array("‎", "‎", "ॅ", "्र", "र्", "ज्ञ", "त्र", "क्ष", "श्र", "‎", "‎", "ः", "ऋ", "औ", "ऐ", "आ", "ई", "ऊ", "भ", "ङ", "घ", "ध", "झ", "ढ", "ञ", "ॠ", "ओ", "ए", "अ", "इ", "उ", "फ", "‎", "ख", "थ", "छ", "ठ", "‎", "ँ", "ण", "‎", "‎", "ळ", "श", "ष", "।", "‎");
var mapSanskritExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॣ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॢ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "॥", "‎");
var mapSanskritShiftExtArray = new Array("‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॡ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ऌ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॐ", "‎", "‎", "‎", "‎", "‎", "‎", "ऽ", "‎");
//***************************Sanskrit Array Ends Here

//***************************santali
var mapsantaliArray = new Array("‎", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "‎", "ौ", "ै", "ा", "ी", "ू", "ब", "ह", "ग", "द", "ज", "ड", "़", "‎", "ो", "े", "्", "ि", "ु", "प", "र", "क", "त", "च", "ट", "‎", "ं", "म", "न", "व", "ल", "स", "‎,", "‎.", "य");
var mapsantaliShiftArray = new Array("‎", "‎", "‎", "्र", "र्", "ज्ञ", "त्र", "क्ष", "श्र", "‎", "‎", "ः", "‎", "औ", "ऐ", "आ", "ई", "ऊ", "भ", "ङ", "घ", "ध", "झ", "ढ", "ञ", "‎", "ओ", "ए", "अ", "इ", "उ", "फ", "‎", "ख", "थ", "छ", "ठ", "‎", "ँ", "ण", "‎", "‎", "‎", "‎", "‎", "।", "‎");
var mapsantaliExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‍", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "॰", "॥", "‎");
var mapsantaliShiftExtArray = new Array("‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॐ", "‎", "‎", "‎", "ळ", "‎", "‎", "ऽ", "‎");
//***************************santali Array Ends Here

//***************************SindhiDev
var mapSindhiDevArray = new Array("‎", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "ृ", "ौ", "ै", "ा", "ी", "ू", "ब", "ह", "ग", "द", "ज", "ड", "़", "‎", "ो", "े", "्", "ि", "ु", "प", "र", "क", "त", "च", "ट", "‎", "ं", "म", "न", "व", "ल", "स", "‎,", "‎.", "य");
var mapSindhiDevShiftArray = new Array("‎", "‎", "‎", "्र", "र्", "ज्ञ", "त्र", "क्ष", "श्र", "‎", "‎", "‎", "ऋ", "औ", "ऐ", "आ", "ई", "ऊ", "भ", "ङ", "घ", "ध", "झ", "ढ", "ञ", "‎", "ओ", "ए", "अ", "इ", "उ", "फ", "ॻ", "ख", "थ", "छ", "ठ", "‎", "‎", "ण", "ॾ", "ॿ", "ॼ", "श", "ष", "।", "‎");
var mapSindhiDevExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॄ", "‎", "‎", "‎", "ॣ", "‎", "‎", "‎", "ग़", "‎", "ज़", "ड़", "‎", "‎", "‎", "‎", "‎", "ॢ", "‎", "‎", "‎", "क़", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "॰", "॥", "‎");
var mapSindhiDevShiftExtArray = new Array("‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ॠ", "‎", "‎", "‎", "ॡ", "‎", "‎", "‎", "‎", "‎", "‎", "ढ़", "‎", "‎", "‎", "‎", "‎", "ऌ", "‎", "फ़", "‎", "ख़", "‎", "‎", "‎", "‎", "ॐ", "‎", "‎", "‎", "‎", "‎", "‎", "ऽ", "‎");
//***************************SindhiDev Array Ends Here

//***************************Tamil
var mapTamilArray = new Array("ொ", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "‎", "ௌ", "ை", "ா", "ீ", "ூ", "‎", "ஹ", "‎", "‎", "ஜ", "‎", "‎", "‎", "ோ", "ே", "்", "ி", "ு", "ப", "ர", "க", "த", "ச", "ட", "ெ", "ஂ", "ம", "ந", "வ", "ல", "ஸ", ",‎", ".‎", "ய");
var mapTamilShiftArray = new Array("ஒ", "‎", "‎", "‎", "‎", "‎", "‎", "க்ஷ", "ஷ்ர", "‎", "‎", "ஃ", "‎", "ஔ", "ஐ", "ஆ", "ஈ", "ஊ", "‎", "ங", "‎", "‎", "‎", "‎", "ஞ", "‎", "ஓ", "ஏ", "அ", "இ", "உ", "‎", "ற", "‎", "‎", "‎", "‎", "எ", "‎", "ண", "ன", "ழ", "ள", "ஶ", "ஷ", "।", "‎");
var mapTamilExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "௰", "௱", "௲", "‎", "‎", "‎", "‎", "‎", "௷", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "௶", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "௴", "௳", "௵", "‎", "‎", "௹", "௥", "‎");
var mapTamilShiftExtArray = new Array("‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ௐ", "௸", "௺", "‎", "‎", "‎", "‎", "‎", "‎");
//***************************Tamil Array Ends Here

//***************************Telugu
var mapTeluguArray = new Array("ొ", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "‎", "ృ", "ౌ", "ై", "ా", "ీ", "ూ", "బ", "హ", "గ", "ద", "జ", "డ", "‎", "‎", "ో", "ే", "్", "ి", "ు", "ప", "ర", "క", "త", "చ", "ట", "ె", "ం", "మ", "న", "వ", "ల", "స", ",‎", ".‎", "య");
var mapTeluguShiftArray = new Array("ట", "‎", "‎", "్ర", "‎", "జ్ఞ", "త్ర", "క్ష", "శ్ర", "‎", "‎", "ః", "ఋ", "ఔ", "ఐ", "ఆ", "ఈ", "ఊ", "భ", "ఙ", "ఘ", "ధ", "ఝ", "ఢ", "ఞ", "‎", "ఓ", "ఏ", "అ", "ఇ", "ఉ", "ఫ", "ఱ", "ఖ", "థ", "ఛ", "ఠ", "ఎ", "ఁ", "ణ", "‎", "‎", "ళ", "శ", "ష", "।", "‎");
var mapTeluguExtArray = new Array("‎", "", "", "‎", "₹", "‎", "‎", "‎", "‎", "‎", "‎", "౿", "ౄ", "‎", "‎", "‎", "ౣ", "‎", "‎", "‎", "‎", "‎", "ౙ", "‎", "‎", "‎", "‎", "‎", "‎", ",ౢ", "‎", "‎", "‎", "‎", "‎", "ౘ", "‎", "ౕ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "॥", "‎");
var mapTeluguShiftExtArray = new Array("‎", "౹", "౼", "౺", "౽", "౻", "౾", "‎", "‎", "‎", "౸", "‎", "ౠ", "‎", "‎", "‎", "ౡ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ౡ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ౖ", "‎", "‎", "‎", "‎", "‎", "‎", "‎", "ఽ", "‎");
//***************************Telugu Array Ends Here