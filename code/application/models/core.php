<?php
/*
 *         الفراهيدي
 *         www.faraheedy.com
 *         نواة لمشروع الشاعر الرقمي - المرحلة 2
 *         مهمة هذا الصف وزن الأشطر الشعريّة
 *         و تقطيعها و كتابتها كتابةً عروضيَّة
 *         فكرة و برمجة
 *         مُختار سيِّد صالح
 *         سوريا - دمشق
 *         00963944467547
 *         Mokhtar_ss@hotmail.com
 *         (C) جميع الحقوق محفوظة  2010 
 *         بدء العمل في تحليله 
 *         و وضع أفكاره عام 2008 
 *         و تم البدء في برمجته في
 *         20/2/2010
 *         و انتهت البرمجة في 
 *         24/2/2010
 *         ثمّ خضع للاختبارات و التدقيق و التنقيح حتّى تاريخ 1/5/2010
 *		   PHP بدء العمل في تحويله إلى لغة
 *		   يوم الثلاثاء 4/6/2013 في البوكمال
 *         انتهينا من جزء القصيدة العمودية منه في 9-6-2013 في البوكمال
 *         تم البدء بجزء القصيدة التفعيلية منه في 10-6-2013 في البوكمال
 *	       و تم الانتهاء منه في 13-6-2013م
 *		   تم البدء في محلل القوافي في البوكمال بتاريخ 30/9/2013
 *		   و تم الانتهاء منه بتاريخ 6/10/2013
 *			تم الانتهاء من معالجات الكتابة في البوكمال بتاريخ
 *			17/10/2013
 *
 *			Bug Fixed in 17-3-2014 // hamzet al-wasel
 */
 
 
class core extends CI_Model  {
    
	function __construct(){
        parent::__construct();
    }
	
	private static $alphabet=array('ا','أ','إ','آ','ء','ئ','ؤ','ى','ب','ت','ة','ث','ج','ح','خ','د','ذ','ر','ز','ش','س','ص','ض','ط','ظ','ع','غ','ف','ق','ك','ل','م','ن','ه','و','ي','#'); //مصفوفة الأحرف # هو الفراغ!
	private static $harakat=array('ّ','َ','ُ','ِ','ً','ٌ','ٍ','ْ'); //مصفوفة الحركات
	
	// convert string 2 chars array
	// while # represents (begin) and (end) and (space)
	private function _str2chars($t){
		$result=array();
		//$t=preg_split('//',$t);
		$t=str_replace(' ','#',$t);
		$t=str_split($t);
		for($i=0;$i<sizeof($t)-1;$i++) // كل حرفين يونيكود معاً هما حرف عربي واحد !!
		{
			if(strcmp($t[$i],'#')!=0 &&
			   strcmp($t[$i+1],'#')!=0)
			{
				$char=$t[$i].$t[$i+1];
				array_push($result,$char);
				$i++;
			}
			else if(strcmp($t[$i],'#')==0)
			{
				$char=$t[$i];
				array_push($result,$char);
			}
		}
		
		return $result;
		
	}
	
	// input will be cleaning also from non-alpha-harakat
	// and any addition space will be deleted
	private function _cleanStr($t){
		if(strcmp($t[0],'#')!=0)
		{
			$result=array('#');
		}
		else
		{
			$result=array();
		}
		
		while(strstr($t,' '))
		{
			$t=str_replace(' ','#',$t);
		}
		
		while(strstr($t,'##'))
		{
			$t=str_replace('##','#',$t);
		}
		
		// إزالة علامات الترقيم
		$punctuations=array('؟','?','/','\\','!',':','-','"',')','(',',',',');
		foreach($punctuations as $p)
		{
			while(strstr($t,$p))
			{
				$t=str_replace($p,'',$t);
			}
		}
		
		$t=self::_str2chars($t);
		for($i=0;$i<sizeof($t);$i++) // كل حرفين مشفرات هن حرف عادي !
		{
			if(in_array($t[$i],self::$alphabet)===true ||
			   in_array($t[$i],self::$harakat)===true)
			{
				array_push($result,$t[$i]);
			}
		}
		
		if(strcmp($t[count($t)-1],'#')!=0)
		{
			array_push($result,'#');
		}
		//var_dump(implode($result));
		return implode($result);
	}
	
	// special cases 
	// waw amro - waw aljame3 - lafez aljalalleh
	// asma2 elesharah - asma2 mawsoolah
	// dawood - tawoos - nawoos !
	private function _specialCases($t){
		$t=self::_cleanStr($t);
		
		$patterns=array();
		$replacements=array();
		
		// واو الجمع
		array_push($patterns,"/و[َُِْ]*ا#/");
		array_push($replacements,"وْ#");
		
		// واو عمرو
		array_push($patterns,"/#عمرٍو#/");
		array_push($replacements,"#عمْرٍ#");
		
		array_push($patterns,"/#عمروٍ#/");
		array_push($replacements,"#عمْرٍ#");
		
		array_push($patterns,"/#عمرًو#/");
		array_push($replacements,"#عمْرً#");
		
		array_push($patterns,"/#عمروً#/");
		array_push($replacements,"#عمْرً#");

		array_push($patterns,"/#عمرٌو#/");
		array_push($replacements,"#عمْرٌ#");

		array_push($patterns,"/#عمروٌ#/");
		array_push($replacements,"#عمْرٌ#");

		array_push($patterns,"/#عمرو#/");
		array_push($replacements,"#عمْر#");

		// إعادة المدّ إلى أصلهِ
		array_push($patterns,"/آ/");
		array_push($replacements,"أا");
		

		// معالجة لفظ الجلالة
		array_push($patterns,"/ى#الله#/");
		array_push($replacements,"لّاه#");
		
		array_push($patterns,"/تالله#/");
		array_push($replacements,"تلّاه#");
		
		array_push($patterns,"/ا#الله#/");
		array_push($replacements,"لّاه#");
		
		array_push($patterns,"/اللهُ#/");
		array_push($replacements,"الْلاهُ#");
		
		array_push($patterns,"/اللهَ#/");
		array_push($replacements,"الْلاهَ#");
		
		array_push($patterns,"/اللهِ#/");
		array_push($replacements,"الْلاهِ#");
		
		array_push($patterns,"/الله#/");
		array_push($replacements,"الْلاه#");
		
		array_push($patterns,"/للهِ#/");
		array_push($replacements,"للْلاهِ#");
		
		// لفظ الجلالة المجرور باللام
		array_push($patterns,"/لله#/");
		array_push($replacements,"للْلاه#");
		
		// اللهمّ
		array_push($patterns,"/#الل[َّ]*هم([َّ]*)#/");
		array_push($replacements,"#الْلاهم\${1}#");
		
		
		// الإله
		array_push($patterns,"/#الإله([َُِْ]*)#/");
		array_push($replacements,"#الإلاه\${1}#");
		
		// للإله
		array_push($patterns,"/#لل[ْ]*إله([َُِْ]*)#/");
		array_push($replacements,"للْإلاه\${1}#");
		
		// إله 
		array_push($patterns,"/#إله([َُِْ]*)([يهمنا])([َُِْ]*)#/"); // "يهمنا" هي حروف الضمائر مثل إلهي , إلههم , إلههن , إلهنا ... و للمصادفة فقد كانت الحروف تشكل كلمة يهمنا
		array_push($replacements,"#إلاه\${1}\${2}\${3}#");
				
		
		// الرحمن
		array_push($patterns,"/الر[َّ]*حمن([َُِْ]*)#/");
		array_push($replacements,"الرَّحْمان\${1}#");
		
		// للرَّحمن
		array_push($patterns,"/للر[َّ]*حمن([َُِْ]*)#/");
		array_push($replacements,"لِرَّحْمان\${1}#");
		
		//
        // معالجة أسماء الإشارة
		//
		
		// هذا
		array_push($patterns,"/#([فلكب]*)ه[َ]*ذ[َ]*ا[ْ]*#/");
		array_push($replacements,"#\${1}هَاذَا#");
		
		// هذه
		array_push($patterns,"/#([فلكب]*)ه[َ]*ذ[ِ]*ه([َُِ]*)#/");
		array_push($replacements,"#\${1}هَاذِه\${2}#");
		
		// هؤلاء
		array_push($patterns,"/#([فلكب]*)ه[َُِ]*ؤ[َُِ]*ل[َِ]*ا[ْ]*ء([َُِْ]*)#/");
		array_push($replacements,"#\${1}هَاؤُلَاء\${2}#");
		
		// ذلك
		array_push($patterns,"/#([فلكب]*)ذ[َُِ]*ل[َُِ]*ك([َِ]*)#/");
		array_push($replacements,"#\${1}ذَالِك\${2}#");
		
		// هذي
		array_push($patterns,"/#([فلكب]*)ه[َُِ]*ذ[َُِ]*ي([َِ]*)#/");
		array_push($replacements,"#\${1}هَاذِي\${2}#");
		
		// هذان
		array_push($patterns,"/#([فلكب]*)ه[َُِ]*ذ[َِ]*ا[ْ]*ن([َُِْ]*)#/");
		array_push($replacements,"#\${1}هَاذَان\${2}#");
		
		// هذين
		array_push($patterns,"/#([فلكب]*)ه[َُِ]*ذ[َِ]*ي[ْ]*ن([َُِْ]*)#/");
		array_push($replacements,"#\${1}هَاذَيْن\${2}#");
		
		// ههنا
		array_push($patterns,"/#([فلكب]*)ه[َُِ]*ه[َِ]*ن[ْ]*ا([َُِْ]*)#/");
		array_push($replacements,"#\${1}هَاهُنَا#");
		
		// ههناك
		array_push($patterns,"/#([فلكب]*)ه[َُِ]*ه[َِ]*ن[ْ]*ا[ْ]*ك([َُِْ]*)#/");
		array_push($replacements,"#\${1}هَاهُنَاك\${2}#");
		
		// هكذا
		array_push($patterns,"/#([فلكب]*)ه[َُِ]*ك[َِ]*ذ[ْ]*ا([َُِْ]*)#/");
		array_push($replacements,"#\${1}هَاكَذَا#");
		
		// لكن ساكنة النون
		array_push($patterns,"/#ل[َُِ]*ك[َِ]*ن([ْ]*)#/");
		array_push($replacements,"#لَاْكِنْ#");
		
		// لكنّ بتشديد النون
		array_push($patterns,"/#ل[َُِ]*ك[َِ]*ن([ّ]*)#/");
		array_push($replacements,"#لَاْكِنْنَ#");
		
		// لكنّ+ضمير
		array_push($patterns,"/#ل[َُِ]*ك[َِ]*نّ(ه[َُِ]*|ه[َُِ]*ا[َُِ]*|ه[َُِ]*م[َُِ]*|ه[َُِ]*ن[َُِ]*|ي[َُِ]*|ن[َُِ]*ا[َُِ]*|ك[َُِ]*|ك[َُِ]*ن[َُِ]*|ك[َُِ]*م[َُِ]*|ه[َُِ]*م[َُِ]*ا)#/");
		array_push($replacements,"#لَاْكِنْنَ\${1}#");
		
		// الذي
		array_push($patterns,"/#ا[َُِ]*ل[َُِ]*ذ[َُِ]*ي([َُِْ]*)#/");
		array_push($replacements,"#اللّذِيْ#");
		
		// فالذي | بالذي | كالذي 
		array_push($patterns,"/#([فبك]*)ا[َُِ]*ل[َُِ]*ذ[َُِ]*ي([َُِْ]*)#/");
		array_push($replacements,"#\${1}اللّذِيْ#");
		
		// للذي 
		array_push($patterns,"/#ل[َُِ]*ل[َُِ]*ذ[َُِ]*ي([َُِْ]*)#/");
		array_push($replacements,"#لِلْلَذِيْ#");
		
		// التي
		array_push($patterns,"/#ا[َُِ]*ل[َُِ]*ت[َُِ]*ي([َُِْ]*)#/");
		array_push($replacements,"#اللّتِيْ#");
		
		// فالتي | بالتي | كالتي
		array_push($patterns,"/#([فبك]*)ا[َُِ]*ل[َُِ]*ت[َُِ]*ي([َُِْ]*)#/");
		array_push($replacements,"#\${1}اللّتِيْ#");
		
		// للتي 
		array_push($patterns,"/#ل[َُِ]*ل[َُِ]*ت[َُِ]*ي([َُِْ]*)#/");
		array_push($replacements,"#لِلْلَتِيْ#");
		
		// الذين
		array_push($patterns,"/#ا[َُِ]*ل[َُِ]*ذ[َُِ]*ي[َُِ]*ن([َِ]*)#/");
		array_push($replacements,"#اللّذِيْنَ#");
		
		// فاللذين | كاللذين | باللذين
		array_push($patterns,"/#([فبك]*)ا[َُِ]*ل[َُِ]*ذ[َُِ]*ي[َُِ]*ن([َِ]*)#/");
		array_push($replacements,"#\${1}اللّذِيْنَ#");
		
		// للذين 
		array_push($patterns,"/#ل[َُِ]*ل[َُِ]*ذ[َُِ]*ي[َُِ]*ن([َِ]*)#/");
		array_push($replacements,"#لِلْلَذِيْنَ#");
		
		// داود 
		array_push($patterns,"/#د[َُِ]*ا[َُِ]*و[َُِ]*د([ٌٍَِ]*|[اً]*)#/");
		array_push($replacements,"#دَاوُوْد\${1}#");
		
		// طاوس 
		array_push($patterns,"/#ط[َُِ]*ا[َُِ]*و[َُِ]*س([ٌٍَِ]*|[اً]*)#/");
		array_push($replacements,"#طَاوُوْس\${1}#");
		
		// ناوس 
		array_push($patterns,"/#ن[َُِ]*ا[َُِ]*و[َُِ]*س([ٌٍَِ]*|[اً]*)#/");
		array_push($replacements,"#نَاوُوْس\${1}#");
		
		
		
		// طه 
		array_push($patterns,"/#ط[َُِ]*ه[َُِ]*#/");
		array_push($replacements,"#طاها#");
		
		$t=preg_replace($patterns,$replacements,$t);
		
		return $t;
		
		
		/*
        تأكد من موضوع هتين أم هاتين
        Result = Result.Replace("هتين ", "هاتيْن ");
		*/
	}
	
	// moony & sunny lam !
	private function _moonySunnyLam($t){
		$t=self::_cleanStr($t);

		$patterns=array();
		$replacements=array();
		
		// ال مع همزة وصل في بداية الشطر
		$tArr=self::_str2chars($t);
		if(strcmp($tArr[0],'#')==0 &&
		   strcmp($tArr[1],'ا')==0 &&
		   strcmp($tArr[2],'ل')==0 &&
		   strcmp($tArr[3],'ا')==0)
		{
			$tArr[0]='#';
			$tArr[1]='أ';
			$tArr[2]='ل';
			$tArr[3]='ِ';
		}
		
		
		// ال قمرية في بداية الشطر
		else if(strcmp($tArr[0],'#')==0 &&
			   strcmp($tArr[1],'ا')==0 &&
			   strcmp($tArr[2],'ل')==0 &&
			   (strcmp($tArr[3],'أ')==0 || strcmp($tArr[3],'إ')==0 || strcmp($tArr[3],'ب')==0 || strcmp($tArr[3],'غ')==0 || strcmp($tArr[3],'ح')==0 || strcmp($tArr[3],'ج')==0 || strcmp($tArr[3],'ك')==0 || strcmp($tArr[3],'و')==0 || strcmp($tArr[3],'خ')==0 || strcmp($tArr[3],'ف')==0 || strcmp($tArr[3],'ع')==0 || strcmp($tArr[3],'ق')==0 || strcmp($tArr[3],'ي')==0 || strcmp($tArr[3],'م')==0 || strcmp($tArr[3],'ه')==0))
		{
			$tArr[0]='#';
			$tArr[1]='أ';
			$tArr[2]='لْ';
		}// لل قمرية
		else if(strcmp($tArr[0],'#')==0 &&
			   strcmp($tArr[1],'ل')==0 &&
			   strcmp($tArr[2],'ل')==0 &&
			   (strcmp($tArr[3],'أ')==0 || strcmp($tArr[3],'إ')==0 || strcmp($tArr[3],'ب')==0 || strcmp($tArr[3],'غ')==0 || strcmp($tArr[3],'ح')==0 || strcmp($tArr[3],'ج')==0 || strcmp($tArr[3],'ك')==0 || strcmp($tArr[3],'و')==0 || strcmp($tArr[3],'خ')==0 || strcmp($tArr[3],'ف')==0 || strcmp($tArr[3],'ع')==0 || strcmp($tArr[3],'ق')==0 || strcmp($tArr[3],'ي')==0 || strcmp($tArr[3],'م')==0 || strcmp($tArr[3],'ه')==0))
		{
			$tArr[0]='#';
			$tArr[1]='ل';
			$tArr[2]='لْ';
		} // ال قمرية قبلها فاء
		else if(strcmp($tArr[0],'#')==0 &&
			   strcmp($tArr[1],'ف')==0 &&
			   strcmp($tArr[2],'ا')==0 &&
			   strcmp($tArr[3],'ل')==0 &&
			   (strcmp($tArr[4],'أ')==0 || strcmp($tArr[4],'إ')==0 || strcmp($tArr[4],'ب')==0 || strcmp($tArr[4],'غ')==0 || strcmp($tArr[4],'ح')==0 || strcmp($tArr[4],'ج')==0 || strcmp($tArr[4],'ك')==0 || strcmp($tArr[4],'و')==0 || strcmp($tArr[4],'خ')==0 || strcmp($tArr[4],'ف')==0 || strcmp($tArr[4],'ع')==0 || strcmp($tArr[4],'ق')==0 || strcmp($tArr[4],'ي')==0 || strcmp($tArr[4],'م')==0 || strcmp($tArr[4],'ه')==0))
		{
			$tArr[0]='#';
			$tArr[1]='x'; // to be cleaned later !!
			unset($tArr[1]);
			$tArr[2]='ف';
			$tArr[3]='لْ';
		}// ال قمرية قبلها باء
		else if(strcmp($tArr[0],'#')==0 &&
			   strcmp($tArr[1],'ب')==0 &&
			   strcmp($tArr[2],'ا')==0 &&
			   strcmp($tArr[3],'ل')==0 &&
			   (strcmp($tArr[4],'أ')==0 || strcmp($tArr[4],'إ')==0 || strcmp($tArr[4],'ب')==0 || strcmp($tArr[4],'غ')==0 || strcmp($tArr[4],'ح')==0 || strcmp($tArr[4],'ج')==0 || strcmp($tArr[4],'ك')==0 || strcmp($tArr[4],'و')==0 || strcmp($tArr[4],'خ')==0 || strcmp($tArr[4],'ف')==0 || strcmp($tArr[4],'ع')==0 || strcmp($tArr[4],'ق')==0 || strcmp($tArr[4],'ي')==0 || strcmp($tArr[4],'م')==0 || strcmp($tArr[4],'ه')==0))
		{
			$tArr[0]='#';
			$tArr[1]='x'; // to be cleaned later !!
			unset($tArr[1]);
			$tArr[2]='ب';
			$tArr[3]='لْ';
		}// ال قمرية قبلها كاف
		else if(strcmp($tArr[0],'#')==0 &&
			   strcmp($tArr[1],'ك')==0 &&
			   strcmp($tArr[2],'ا')==0 &&
			   strcmp($tArr[3],'ل')==0 &&
			   (strcmp($tArr[4],'أ')==0 || strcmp($tArr[4],'إ')==0 || strcmp($tArr[4],'ب')==0 || strcmp($tArr[4],'غ')==0 || strcmp($tArr[4],'ح')==0 || strcmp($tArr[4],'ج')==0 || strcmp($tArr[4],'ك')==0 || strcmp($tArr[4],'و')==0 || strcmp($tArr[4],'خ')==0 || strcmp($tArr[4],'ف')==0 || strcmp($tArr[4],'ع')==0 || strcmp($tArr[4],'ق')==0 || strcmp($tArr[4],'ي')==0 || strcmp($tArr[4],'م')==0 || strcmp($tArr[4],'ه')==0))
		{
			$tArr[0]='#';
			$tArr[1]='x'; // to be cleaned later !!
			unset($tArr[1]);
			$tArr[2]='ك';
			$tArr[3]='لْ';
		}// ال الشمسية في بداية الشطر
		else if(strcmp($tArr[0],'#')==0 &&
			   strcmp($tArr[1],'ا')==0 &&
			   strcmp($tArr[2],'ل')==0)
		{
			$tArr[0]='#';
			$tArr[1]='أ';
			if(strcmp($tArr[3],'ّ')!=0)
			{
				$tmp=array();
				array_push($tmp,$tArr[3]);
				array_push($tmp,'ّ');
				unset($tArr[2]);
				$tArr[3]=implode($tmp);
			}
		}//
		
		
		// تزبيط بداية السلسلة النصية
		if(strcmp($tArr[0],'#')!=0)
		{
			$tmp=array();
			array_push($tmp,'#');
			array_push($tmp,$tArr[0]);
			$tArr[0]=implode($tmp);
		}
		$xx=array_pop($tArr);
		if(strcmp($xx,'#')!=0)
		{
			array_push($tArr,$xx);
			array_push($tArr,'#');
		}
		else
		{
			array_push($tArr,'#');
		}
		$t=implode($tArr);
		
		
		
		
		
		
		// اللام الشمسيَّة
		array_push($patterns,"/و#ال(ت|ث|د|ذ|ر|ز|س|ش|ص|ض|ط|ظ|ل|ن)/");
		array_push($replacements,"و#\${1}ّ");
		
		// اللام الشمسيَّة قبلها حرف يحذف
		array_push($patterns,"/(ا[َُِْ]*|ى[َُِْ]*|ي[ُِْ]*|وْ)#ال(ت|ث|د|ذ|ر|ز|س|ش|ص|ض|ط|ظ|ل|ن)/");
		array_push($replacements,"#\${2}ّ");
		
		// اللام الشمسيَّة قبلها ياء مفتوحة أو ياء مشدّدة
		array_push($patterns,"/(ي[َّ]*)#ال(ت|ث|د|ذ|ر|ز|س|ش|ص|ض|ط|ظ|ل|ن)/");
		array_push($replacements,"\${1}#\${2}ّ");
		
		// اللام الشمسيَّة قبلها تاء مربوطة
		array_push($patterns,"/ة([َُِ]*)#ال(ت|ث|د|ذ|ر|ز|س|ش|ص|ض|ط|ظ|ل|ن)/");
		array_push($replacements,"ت\${1}#\${2}ّ");
		
		// اللام الشمسيَّة
		array_push($patterns,"/#([فكب]*)ال(ت|ث|د|ذ|ر|ز|س|ش|ص|ض|ط|ظ|ل|ن)/");
		array_push($replacements,"#\${1}\${2}ّ");
		
		// اللام الشمسية مجرورة بحرف اللام
		array_push($patterns,"/#لل(ت|ث|د|ذ|ر|ز|س|ش|ص|ض|ط|ظ|ل|ن)/");
		array_push($replacements,"ل#\${1}ّ");
		
		
		// همزة الوصل
		array_push($patterns,"/#ال(ا)/");
		array_push($replacements,"#لِ");
		
		
		// لام قمرية قبلها حرف يحذف
		//array_push($patterns,"/(ا[َُِْ]*|ى[َُِْ]*|يْ|وْ)#ال([أ|إ|ب|غ|ح|ج|ك|و|خ|ف|ع|ق|ي|م|ه])/");
		//array_push($replacements,"#لْ\${2}");
		
		// لام قمرية قبلها حرف يحذف
		array_push($patterns,"/(ا[َُِْ]*|ى[َُِْ]*|ي[ُِْ]*|وْ)#ال([أ|إ|ب|غ|ح|ج|ك|و|خ|ف|ع|ق|ي|م|ه])/");
		array_push($replacements,"#لْ\${2}");
		
		// اللام القمرية
		array_push($patterns,"/#([فكب]*)ال(أ|إ|ب|غ|ح|ج|ك|و|خ|ف|ع|ق|ي|م|ه)/");
		array_push($replacements,"#\${1}لْ\${2}");
		
		array_push($patterns,"/#ال([أ|إ|ب|غ|ح|ج|ك|و|خ|ف|ع|ق|ي|م|ه])/");
		array_push($replacements,"#ألْ\${1}");
		
		// لام قمريّة مجرورة بحرف اللام
		array_push($patterns,"/#لل([أ|إ|ب|غ|ح|ج|ك|و|خ|ف|ع|ق|ي|م|ه])/");
		array_push($replacements,"#للْ\${1}");
		
		
		$t=preg_replace($patterns,$replacements,$t);

		return $t;
	}
	
	// tanween and shaddeh
	// eshba3 a5er alshater
	private function _tanweenShaddeh($t,$isAjez){
		$t=self::_cleanStr($t);
		
		$tArr=self::_str2chars($t);
		
		if(count($tArr)<1) // أمان من أجل الدخل الخبيث !!
		{
			return implode($tArr);
		}
		
		// معالجة الشدّات
		for($i=0;$i<count($tArr);$i++)
		{
			if(strcmp($tArr[$i],'ّ')==0 &&
			   in_array($tArr[$i-1],self::$alphabet)===true)
			{
				$tArr[$i]='ْ'.$tArr[$i-1];
			}
		}
		
		// إشباع آخر الشطر في القافية المطلقة
		if(strcmp($tArr[count($tArr)-1],'ْ')!=0 &&
			(strcmp($tArr[count($tArr)-1],'ا')==0 || strcmp($tArr[count($tArr)-1],'ى')==0))
		{
			array_push($tArr,'ْ');
		}
		
		
		if($isAjez)
		{
			if(strcmp($tArr[count($tArr)-1],'ْ')!=0 && strcmp($tArr[count($tArr)-1],'ٌ')!=0 && strcmp($tArr[count($tArr)-1],'ً')!=0 && strcmp($tArr[count($tArr)-1],'ٍ')!=0)
			{
				$chr='وْ';
				if(strcmp($tArr[count($tArr)-1],'َ')==0)
				{
					$chr="اْ";
				}
				else if(strcmp($tArr[count($tArr)-1],'ِ')==0)
				{
					$chr="يْ";
				}
				else if(strcmp($tArr[count($tArr)-1],'ُ')==0)
				{
					$chr="وْ";
				}
				array_push($tArr,$chr);
			}
		}
		$t=implode($tArr);
		
		
		// التنوين يا ملك
		$patterns=array();
		$replacements=array();
		
		array_push($patterns,"/اً/");
		array_push($replacements,"نْ");
		
		array_push($patterns,"/ةٌ/");
		array_push($replacements,"تُنْ");
		
		array_push($patterns,"/ةً/");
		array_push($replacements,"تَنْ");
		
		array_push($patterns,"/ةٍ/");
		array_push($replacements,"تِنْ");
		
		array_push($patterns,"/ىً/");
		array_push($replacements,"نْ");
		
		array_push($patterns,"/ٌ|ٍ|ً/");
		array_push($replacements,"نْ");
		
		$t=preg_replace($patterns,$replacements,$t);
		
		
		
		$t=str_replace('ّ','',$t); // بالعضل خوفاً من بقاء شدة بالخطأ !!!
		
		return $t;
	}
	
	// hamzet wasel
	private function _hamzetWasel($t){
		$t=self::_cleanStr($t);
		$tArr=self::_str2chars($t);
		if(count($tArr)<1) // أمان من أجل الدخل الخبيث !!
		{
			return implode($tArr);
		}
		
		// همزة الوصل في بداية الشطر
		if(strcmp($tArr[1],'ا')==0 &&
		   strcmp($tArr[2],'ل')!=0 && strcmp($tArr[3],'ل')!=0)
		{
			$tArr[1]='إِ';
		}
		$t=implode($tArr);
		
		
		$patterns=array();
		$replacements=array();
		
		// ابن
		array_push($patterns,"/([يواى]*)#ا[َُِْ]*ب[َُِْ]*ن/");
		array_push($replacements,"#بْن");
		array_push($patterns,"/#([فكلب]*)ا[َُِْ]*ب[َُِْ]*ن/");
		array_push($replacements,"#\${1}بْن");
		// امرؤ
		array_push($patterns,"/([يواى]*)#ا[َُِْ]*م[َُِْ]*ر/");
		array_push($replacements,"#مْر");
		array_push($patterns,"/#([فكلب]*)ا[َُِْ]*م[َُِْ]*ر/");
		array_push($replacements,"#\${1}مْر");
		// اثنان
		array_push($patterns,"/([يواى]*)#ا[َُِْ]*ث[َُِْ]*ن[َُِْ]*ا[َُِْ]*ن/");
		array_push($replacements,"#ثْنان");
		array_push($patterns,"/#([فكلب]*)ا[َُِْ]*ث[َُِْ]*ن[َُِْ]*ا[َُِْ]*ن/");
		array_push($replacements,"#\${1}ثْنان");
		// اثنين
		array_push($patterns,"/([يواى]*)#ا[َُِْ]*ث[َُِْ]*ن[َُِْ]*ي[َُِْ]*ن/");
		array_push($replacements,"#ثْنيْن");
		array_push($patterns,"/#([فكلب]*)ا[َُِْ]*ث[َُِْ]*ن[َُِْ]*ي[َُِْ]*ن/");
		array_push($replacements,"#\${1}ثْنيْن");
		// اثنتان
		array_push($patterns,"/([يواى]*)#ا[َُِْ]*ث[َُِْ]*ن[َُِْ]*ت[َُِْ]*ا[َُِْ]*ن/");
		array_push($replacements,"#ثْنتان");
		array_push($patterns,"/#([فكلب]*)ا[َُِْ]*ث[َُِْ]*ن[َُِْ]*ت[َُِْ]*ا[َُِْ]*ن/");
		array_push($replacements,"#\${1}ثْنتان");
		// اثنتين
		array_push($patterns,"/([يواى]*)#ا[َُِْ]*ث[َُِْ]*ن[َُِْ]*ت[َُِْ]*ي[َُِْ]*ن/");
		array_push($replacements,"#ثْنتيْن");
		array_push($patterns,"/#([فكلب]*)ا[َُِْ]*ث[َُِْ]*ن[َُِْ]*ت[َُِْ]*ي[َُِْ]*ن/");
		array_push($replacements,"#\${1}ثْنتيْن");
		// است
		array_push($patterns,"/([يواى]*)#ا[َُِْ]*س[َُِْ]*ت([َُِْ]*)/");
		array_push($replacements,"#سْت\${2}");
		array_push($patterns,"/#([فكلب]*)ا[َُِْ]*س[َُِْ]*ت([َُِْ]*)/");
		array_push($replacements,"#\${1}سْت\${2}");
		
		//همزة وصل قبلها حرف يحذف
		array_push($patterns,"/(ا|ي|ى)#ا(أ|إ|ب|ت|ث|ج|ح|خ|د|ذ|ر|ز|س|ش|ص|ض|ط|ظ|ع|غ|ف|ق|ك|م|ن|ه|و|ي)/");
		array_push($replacements,"#\${2}ْ");
		
		//همزة وصل قبلها حرف
		// error in this expression
		// فاجتازوه مثلاً لا يحذف ألفها
		array_push($patterns,"/#([فكلب]*)ا(أ|إ|ب|ت|ث|ج|ح|خ|د|ذ|ر|ز|س|ش|ص|ض|ط|ظ|ع|غ|ف|ق|ك|م|ن|ه|و|ي)([أإبتثجحخدذرزسشصضطظعغفقكلمنهوي]{4,})/");
		array_push($replacements,"#\${1}\${2}ْ\${3}");
		
		//همزة وصل ما قبلها شي
		array_push($patterns,"/#ا(أ|إ|ب|ت|ث|ج|ح|خ|د|ذ|ر|ز|س|ش|ص|ض|ط|ظ|ع|غ|ف|ق|ك|م|ن|ه|و|ي)/");
		array_push($replacements,"#\${1}ْ");
		
			
		
		$t=preg_replace($patterns,$replacements,$t);
		
		$t=str_replace('ْْ','ْ',$t); // بالعضل خوفاً من بقاء سكونين بالغلط !!!
		
		return $t;
	}

	// get chars only
	private function _getCharsOnly($t){
		$t=self::_str2chars($t);
		$res=array();
		for($i=0;$i<count($t);$i++)
		{
			if(in_array($t[$i],self::$alphabet)===true
			   && strcmp($t[$i],'#')!=0)
		    {
			   array_push($res,$t[$i]);
		    }
		}
		return implode($res);
	}
	
	// get harakat only
	private function _getHarakatOnly($t){
		$t=self::_str2chars($t);
		$res=array();
		for($i=0;$i<count($t)-1;$i++)
		{
			if(in_array($t[$i],self::$harakat)===true)
		    {
			   array_push($res,$t[$i]);
		    }
			else if(in_array($t[$i],self::$alphabet)===true
			        && strcmp($t[$i],'#')!=0
			        && in_array($t[$i+1],self::$harakat)===false)
					{
					   if(strcmp($t[$i],'ى')!=0 && strcmp($t[$i],'ا')!=0)
					   {
							array_push($res,'َ');
					   }
					   else
					   {
							array_push($res,'ْ');
					   }
					}
			else if(in_array($t[$i],self::$alphabet)===true
			        && strcmp($t[$i],'#')!=0
			        && in_array($t[$i+1],self::$harakat)===true)
					{
						array_push($res,$t[$i+1]);
						$i++;	
					}
		}
		$res=implode($res);

		$res=str_replace('ِ','َ',$res);
		$res=str_replace('ُ','َ',$res);
		return $res;
	}
	
	// get rokzah khoutait
	private function _rokzahKhoutayt($t){
		$t=str_replace('َْ','-',$t);
		$t=str_replace('َ','U',$t);
		$t=str_replace('ْ','U',$t);
		return $t;
	}
	
	// get ba7er name
	private function _getBa7er($rokaz){
	
		$rokaz="%".$rokaz."%";
		
		//تمثيل البحور بشكل تعابير نظامية
		$taweel="/%U-[-U]U---U-[U-]U(---|-U-|--)%/";
		$baseet="/%(--U-|U-U-)(-U-|UU-)--U-(-U-|UU-|--)%/";
		$madeed="/%[-U]U--[-U]U-(-U--|-U-U|-U-|UU-)%/";
		$kamel="/%(UU|-)-U-(UU|-)-U-(UU-U-|--U-|UU--|---)%/";
		$rajaz="/%(--U-|U-U-|-UU-|UUU-)(--U-|U-U-|-UU-|UUU-)(--U-|U-U-|-UU-|UUU-|---)%/";
		$ramal="/%(-U--|UU--|UU-U|-U-U)(-U--|UU--|UU-U|-U-U)(-U--|-U-|UU-|-U-U)%/";
		$saree3="/%(--U-|U-U-|-UU-|UUU-)(--U-|U-U-|-UU-|UUU-)(-U-|-U-U)%/";
		$khafeef="/%(-U--|UU--)(--U-|U-U-)(-U--|UU--|---|UU-)%/";
		$munsare7="/%(--U-|U-U-|-UU-|UUU-)(---U|-U-U|UU-U)(--U-|-UU-|---)%/";
		$wafer="/%(U-UU-|U---)(U-UU-|U---)(U--)%/";
		$o7othKamel="/%(UU-U-|--U-)(UU-U-|--U-)UU-%/";
		$mutakareb="/%(U--|U-U){3}(U--|U-U|U-)%/";
		$mutadarak="/%(-U-|UU-|--)(-U-|UU-|--)(-U-|UU-|--)(-U-|UU-|--)%/";
		$mu5alla3Baseet="/%(--U-|U-U-|-UU-)-U-U--%/";
		$majzoo2Baseet="/%(--U-|U-U-|-UU-|UUU-)(-U-|UU-)(--U-|---|--U-U)%/";
		$majzoo2Kamel="/%(UU-U-|--U-)(UU-U-|UU--|--U-|UU-U-U|UU-U--)%/";
		$majzoo2Ramal="/%(-U--|UU--)(-U--|UU--|-U--U|-U-)%/";
		$majzoo2Saree3="/%(--U-|U-U-|-UU-|UUU-)(-U-|-U-U)%/";
		$majzoo2khafeef="/%(-U--|UU--)(--U-|U-U-)%/";
		$majzoo2Munsare7="/%(--U-|U-U-|-UU-|UUU-)(---U|---)%/";
		$majzoo2Mutakareb="/%(U--|U-U){2}(U--|U-U|U-|-)%/";
		$majzoo2Mutadarak="/%(-U-|UU-|--){2}(-U-|-U-U|UU--)%/";
		$hazaj="/%(U---|U--U)(U---|U--U)%/";
		$majzoo2Wafer="/%(U-UU-|U---)(U-UU-|U---)%/";
		$majzoo2Rajaz="/%(--U-|U-U-|-UU-|UUU-)(--U-|U-U-|-UU-|UUU-|---|--U--)%/";
		$modare3="/%(U--U|U-U-)-U--%/";
		$moktadab="/%-U-U-UU-%/";
		$mojtath="/%(--U-|U-U-)(-U--|UU--|---)%/";
		$manhookRajaz="/%(--U-|U-U-|-UU-|UUU-|---)%/";
		
		$bo7or=array('taweel'=>$taweel,
					 'baseet'=>$baseet,
					 'rajaz'=>$rajaz,
					 'kamel'=>$kamel,
					 'madeed'=>$madeed,
					
					 'ramal'=>$ramal,
					 'saree3'=>$saree3,
					 'khafeef'=>$khafeef,
					 'o7othKamel'=>$o7othKamel,
					 'munsare7'=>$munsare7,
					 'wafer'=>$wafer,
					
					 'mutakareb'=>$mutakareb,
					 'mutadarak'=>$mutadarak,
					 'mu5alla3Baseet'=>$mu5alla3Baseet,
					 'majzoo2Baseet'=>$majzoo2Baseet,
					 'majzoo2Ramal'=>$majzoo2Ramal,
					 'majzoo2Saree3'=>$majzoo2Saree3,
					 'majzoo2khafeef'=>$majzoo2khafeef,
					 'majzoo2Munsare7'=>$majzoo2Munsare7,
					 'majzoo2Mutakareb'=>$majzoo2Mutakareb,
					 'majzoo2Mutadarak'=>$majzoo2Mutadarak,
					 'hazaj'=>$hazaj,
					 'majzoo2Wafer'=>$majzoo2Wafer,
					 'majzoo2Rajaz'=>$majzoo2Rajaz,
					 'moktadab'=>$moktadab,
					 'mojtath'=>$mojtath,
					 'majzoo2Kamel'=>$majzoo2Kamel,
					 'modare3'=>$modare3,
					 'manhookRajaz'=>$manhookRajaz);
		
		foreach($bo7or as $key => $x)
		{
			if (preg_match($x, $rokaz, $matches))
			{
				return $key;
			}
		}
		
		return "unknown";
	}
	
	// get tafa3eel
	private function _getTafa3eel($rokaz,$chars,$ba7erName){
		$res=array();
		if(strcmp($ba7erName,'taweel')==0)
		{
			$s=substr($rokaz,0,3);
			$i=0;
			
			if(strcmp('U--',$s)==0){
				array_push($res,'فَعُوْلُنْ');
				$t=substr($chars,0,10);
				$i+=10;
				array_push($res,$t);
			}
			else if(strcmp('U-U',$s)==0){
				array_push($res,'فَعُوْلُ');
				$t=substr($chars,0,8);
				$i+=8;
				array_push($res,$t);
			}
			
			array_push($res,'مَفَاْعِيْلُنْ');
			$t=substr($chars,$i,14);
			$i+=14;
			array_push($res,$t);
			
			$s=substr($rokaz,7,3);
			if(strcmp('U--',$s)==0){
				array_push($res,'فَعُوْلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				array_push($res,$t);
			}
			else if(strcmp('U-U',$s)==0){
				array_push($res,'فَعُوْلُ');
				$t=substr($chars,$i,8);
				$i+=8;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,10,strlen($rokaz)-10);
			if(strcmp('U---',$s)==0){
				array_push($res,'مَفَاْعِيْلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مَفَاْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				array_push($res,$t);
			}
			else if(strcmp('U--',$s)==0){
				array_push($res,'فَعُوْلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				array_push($res,$t);
			}
			
		}
		else if(strcmp($ba7erName,'baseet')==0)
		{
			$s=substr($rokaz,0,4);
			$i=0;
			
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				array_push($res,$t);
			}
			else if(strcmp('-UU-',$s)==0){
				array_push($res,'مُسْتَعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				array_push($res,$t);
			}
			
			
			$s=substr($rokaz,4,3);
			if(strcmp('-U-',$s)==0){
				array_push($res,'فَاْعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				array_push($res,$t);
			}
			else if(strcmp('UU-',$s)==0){
				array_push($res,'فَعِلُنْ');
				$t=substr($chars,$i,8);
				$i+=8;
				array_push($res,$t);
			}
			
			array_push($res,'مُسْتَفْعِلُنْ');
			$t=substr($chars,$i,14);
			$i+=14;
			array_push($res,$t);
			
			$s=substr($rokaz,11,3);
			if(strcmp('-U-',$s)==0){
				array_push($res,'فَاْعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				array_push($res,$t);
			}
			else if(strcmp('UU-',$s)==0){
				array_push($res,'فَعِلُنْ');
				$t=substr($chars,$i,8);
				$i+=8;
				array_push($res,$t);
			}
			$s=substr($rokaz,11,2);
			if(strcmp('--',$s)==0){
				array_push($res,'فَاْلُنْ');
				$t=substr($chars,$i,8);
				$i+=8;
				array_push($res,$t);
			}
		}
		else if(strcmp($ba7erName,'kamel')==0)
		{
		
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,5);
			if(strcmp('UU-U-',$s)==0){
				array_push($res,'مُتَفَاْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=5;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,4);
				if(strcmp('--U-',$s)==0){
					array_push($res,'مُسْتَفْعِلُنْ');
					$t=substr($chars,$i,14);
					$i+=14;
					$si+=4;
					array_push($res,$t);
				}
			}
			
			$s=substr($rokaz,$si,5);
			if(strcmp('UU-U-',$s)==0){
				array_push($res,'مُتَفَاْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=5;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,4);
				if(strcmp('--U-',$s)==0){
					array_push($res,'مُسْتَفْعِلُنْ');
					$t=substr($chars,$i,14);
					$i+=14;
					$si+=4;
					array_push($res,$t);
				}
			}
			
			$s=substr($rokaz,$si,5);
			if(strcmp('UU-U-',$s)==0){
				array_push($res,'مُتَفَاْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=5;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,4);
				if(strcmp('--U-',$s)==0){
					array_push($res,'مُسْتَفْعِلُنْ');
					$t=substr($chars,$i,14);
					$i+=14;
					$si+=4;
					array_push($res,$t);
				}
				else if(strcmp('UU--',$s)==0){
					array_push($res,'مُتَفَاْعِلْ');
					$t=substr($chars,$i,12);
					$i+=12;
					$si+=4;
					array_push($res,$t);
				}
				else
				{
					$s=substr($rokaz,$si,3);
					if(strcmp('---',$s)==0){
						array_push($res,'مُسْتَفْعِلْ');
						$t=substr($chars,$i,12);
						$i+=12;
						$si+=3;
						array_push($res,$t);
					}
				}
			}
		}
		else if(strcmp($ba7erName,'madeed')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('-U--',$s)==0){
				array_push($res,'فَاْعِلَاْتُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UU--',$s)==0){
				array_push($res,'فَعِلَاْتُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,3);
			if(strcmp('-U-',$s)==0){
				array_push($res,'فَاْعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			else if(strcmp('UU-',$s)==0){
				array_push($res,'فَعِلُنْ');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('-U--',$s)==0){
				array_push($res,'فَاْعِلَاْتُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-U-U',$s)==0){
				array_push($res,'فَاْعِلَاْتْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else{
				$s=substr($rokaz,$si,3);
				if(strcmp('-U-',$s)==0){
					array_push($res,'فَاْعِلُنْ');
					$t=substr($chars,$i,10);
					$i+=10;
					$si+=3;
					array_push($res,$t);
				}
				else if(strcmp('UU-',$s)==0){
					array_push($res,'فَعِلُنْ');
					$t=substr($chars,$i,8);
					$i+=8;
					$si+=3;
					array_push($res,$t);
				}
			}
		}
		else if(strcmp($ba7erName,'rajaz')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-UU-',$s)==0){
				array_push($res,'مُسْتَعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UUU-',$s)==0){
				array_push($res,'مُتَعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-UU-',$s)==0){
				array_push($res,'مُسْتَعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UUU-',$s)==0){
				array_push($res,'مُتَعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-UU-',$s)==0){
				array_push($res,'مُسْتَعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UUU-',$s)==0){
				array_push($res,'مُتَعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=4;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,3);
				if(strcmp('--U-',$s)==0){
					array_push($res,'مُسْتَفْعِلْ');
					$t=substr($chars,$i,12);
					$i+=12;
					$si+=3;
					array_push($res,$t);
				}
			}
		}
		else if(strcmp($ba7erName,'ramal')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('-U--',$s)==0){
				array_push($res,'فَاْعِلَاْتُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UU--',$s)==0){
				array_push($res,'فَعِلَاْتُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UU-U',$s)==0){
				array_push($res,'فَعِلَاْتُ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-U-U',$s)==0){
				array_push($res,'فَاْعِلَاْتُ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('-U--',$s)==0){
				array_push($res,'فَاْعِلَاْتُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UU--',$s)==0){
				array_push($res,'فَعِلَاْتُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UU-U',$s)==0){
				array_push($res,'فَعِلَاْتُ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-U-U',$s)==0){
				array_push($res,'فَاْعِلَاْتُ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('-U--',$s)==0){
				array_push($res,'فَاْعِلَاْتُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-U-U',$s)==0){
				array_push($res,'فَاْعِلَاْنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,3);
				if(strcmp('-U-',$s)==0){
					array_push($res,'فَاْعِلُنْ');
					$t=substr($chars,$i,10);
					$i+=10;
					$si+=3;
					array_push($res,$t);
				}
				else if(strcmp('UU-',$s)==0){
					array_push($res,'فَعِلُنْ');
					$t=substr($chars,$i,8);
					$i+=8;
					$si+=3;
					array_push($res,$t);
				}
			}
			
		}
		else if(strcmp($ba7erName,'saree3')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-UU-',$s)==0){
				array_push($res,'مُسْتَعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UUU-',$s)==0){
				array_push($res,'مُتَعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-UU-',$s)==0){
				array_push($res,'مُسْتَعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UUU-',$s)==0){
				array_push($res,'مُتَعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('-U-U',$s)==0){
				array_push($res,'فَاْعِلَاْنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,3);
				if(strcmp('-U-',$s)==0){
					array_push($res,'فَاْعِلُنْ');
					$t=substr($chars,$i,10);
					$i+=10;
					$si+=3;
					array_push($res,$t);
				}
			}
		}
		else if(strcmp($ba7erName,'khafeef')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('-U--',$s)==0){
				array_push($res,'فَاْعِلَاْتُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UU--',$s)==0){
				array_push($res,'فَعِلَاْتُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('-U--',$s)==0){
				array_push($res,'فَاْعِلَاْتُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UU--',$s)==0){
				array_push($res,'فَعِلَاْتُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,3);
				if(strcmp('---',$s)==0){
					array_push($res,'فَاْلَاْتُنْ');
					$t=substr($chars,$i,12);
					$i+=12;
					$si+=3;
					array_push($res,$t);
				}
				else if(strcmp('UU-',$s)==0){
					array_push($res,'فَعِلُنْ');
					$t=substr($chars,$i,8);
					$i+=8;
					$si+=3;
					array_push($res,$t);
				}
			}
		}
		else if(strcmp($ba7erName,'munsare7')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-UU-',$s)==0){
				array_push($res,'مُسْتَعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UUU-',$s)==0){
				array_push($res,'مُتَعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('---U',$s)==0){
				array_push($res,'مَفْعُوْلَاْتُ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-U-U',$s)==0){
				array_push($res,'مَفْعِلَاْتُ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UU-U',$s)==0){
				array_push($res,'مَعِلَاْتُ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-UU-',$s)==0){
				array_push($res,'مُسْتَعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,3);
				if(strcmp('---',$s)==0){
					array_push($res,'مُسْتَفْعِلْ');
					$t=substr($chars,$i,12);
					$i+=12;
					$si+=3;
					array_push($res,$t);
				}
			}
		}
		else if(strcmp($ba7erName,'wafer')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,5);
			if(strcmp('U-UU-',$s)==0){
				array_push($res,'مُفَاْعِلَتُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=5;
				array_push($res,$t);
			}
			else{
				$s=substr($rokaz,$si,4);
				if(strcmp('U---',$s)==0){
					array_push($res,'مَفَاْعِيْلُنْ');
					$t=substr($chars,$i,14);
					$i+=14;
					$si+=4;
					array_push($res,$t);
				}
			}
			
			$s=substr($rokaz,$si,5);
			if(strcmp('U-UU-',$s)==0){
				array_push($res,'مُفَاْعِلَتُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=5;
				array_push($res,$t);
			}
			else{
				$s=substr($rokaz,$si,4);
				if(strcmp('U---',$s)==0){
					array_push($res,'مَفَاْعِيْلُنْ');
					$t=substr($chars,$i,14);
					$i+=14;
					$si+=4;
					array_push($res,$t);
				}
			}
			
			$s=substr($rokaz,$si,3);
			if(strcmp('U--',$s)==0){
				array_push($res,'فَعُوْلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			
		}
		else if(strcmp($ba7erName,'o7othKamel')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,5);
			if(strcmp('UU-U-',$s)==0){
				array_push($res,'مُتَفَاْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=5;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,4);
				if(strcmp('--U-',$s)==0){
					array_push($res,'مُسْتَفْعِلُنْ');
					$t=substr($chars,$i,14);
					$i+=14;
					$si+=4;
					array_push($res,$t);
				}
			}
			
			$s=substr($rokaz,$si,5);
			if(strcmp('UU-U-',$s)==0){
				array_push($res,'مُتَفَاْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=5;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,4);
				if(strcmp('--U-',$s)==0){
					array_push($res,'مُسْتَفْعِلُنْ');
					$t=substr($chars,$i,14);
					$i+=14;
					$si+=4;
					array_push($res,$t);
				}
			}
			
			$s=substr($rokaz,$si,3);
			if(strcmp('UU-',$s)==0){
				array_push($res,'فَعِلُنْ');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			
		}
		else if(strcmp($ba7erName,'mutakareb')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,3);
			if(strcmp('U--',$s)==0){
				array_push($res,'فَعُوْلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			else if(strcmp('U-U',$s)==0){
				array_push($res,'فَعُوْلُ');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,3);
			if(strcmp('U--',$s)==0){
				array_push($res,'فَعُوْلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			else if(strcmp('U-U',$s)==0){
				array_push($res,'فَعُوْلُ');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,3);
			if(strcmp('U--',$s)==0){
				array_push($res,'فَعُوْلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			else if(strcmp('U-U',$s)==0){
				array_push($res,'فَعُوْلُ');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,3);
			if(strcmp('U--',$s)==0){
				array_push($res,'فَعُوْلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			else if(strcmp('U-U',$s)==0){
				array_push($res,'فَعُوْل');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,2);
				if(strcmp('U-',$s)==0){
					array_push($res,'فَعُوْ');
					$t=substr($chars,$i,6);
					$i+=6;
					$si+=2;
					array_push($res,$t);
				}
			}
		}
		else if(strcmp($ba7erName,'mutadarak')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,3);
			if(strcmp('-U-',$s)==0){
				array_push($res,'فَاْعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			else if(strcmp('UU-',$s)==0){
				array_push($res,'فَعِلُنْ');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,2);
				if(strcmp('--',$s)==0){
					array_push($res,'فَاْلُنْ');
					$t=substr($chars,$i,8);
					$i+=8;
					$si+=2;
					array_push($res,$t);
				}
			}
			
			$s=substr($rokaz,$si,3);
			if(strcmp('-U-',$s)==0){
				array_push($res,'فَاْعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			else if(strcmp('UU-',$s)==0){
				array_push($res,'فَعِلُنْ');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,2);
				if(strcmp('--',$s)==0){
					array_push($res,'فَاْلُنْ');
					$t=substr($chars,$i,8);
					$i+=8;
					$si+=2;
					array_push($res,$t);
				}
			}
			
			$s=substr($rokaz,$si,3);
			if(strcmp('-U-',$s)==0){
				array_push($res,'فَاْعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			else if(strcmp('UU-',$s)==0){
				array_push($res,'فَعِلُنْ');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,2);
				if(strcmp('--',$s)==0){
					array_push($res,'فَاْلُنْ');
					$t=substr($chars,$i,8);
					$i+=8;
					$si+=2;
					array_push($res,$t);
				}
			}
			
			$s=substr($rokaz,$si,3);
			if(strcmp('-U-',$s)==0){
				array_push($res,'فَاْعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			else if(strcmp('UU-',$s)==0){
				array_push($res,'فَعِلُنْ');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,2);
				if(strcmp('--',$s)==0){
					array_push($res,'فَاْلُنْ');
					$t=substr($chars,$i,8);
					$i+=8;
					$si+=2;
					array_push($res,$t);
				}
			}
		}
		else if(strcmp($ba7erName,'mu5alla3Baseet')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-UU-',$s)==0){
				array_push($res,'مُسْتَعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,3);
			if(strcmp('-U-',$s)==0){
				array_push($res,'فَاْعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,3);
			if(strcmp('U--',$s)==0){
				array_push($res,'فَعُوْلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
		}
		else if(strcmp($ba7erName,'majzoo2Baseet')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-UU-',$s)==0){
				array_push($res,'مُسْتَعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UUU-',$s)==0){
				array_push($res,'مُتَعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,3);
			if(strcmp('-U-',$s)==0){
				array_push($res,'فَاْعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			else if(strcmp('UU-',$s)==0){
				array_push($res,'فَعِلُنْ');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,5);
			if(strcmp('--U-U',$s)==0){
				array_push($res,'مُسْتَفْعِلَاْنْ');
				$t=substr($chars,$i,16);
				$i+=16;
				$si+=5;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,4);
				if(strcmp('--U-',$s)==0){
					array_push($res,'مُسْتَفْعِلُنْ');
					$t=substr($chars,$i,14);
					$i+=14;
					$si+=4;
					array_push($res,$t);
				}
				else
				{
					$s=substr($rokaz,$si,3);
					if(strcmp('---',$s)==0){
						array_push($res,'مُسْتَفْعِلْ');
						$t=substr($chars,$i,12);
						$i+=12;
						$si+=3;
						array_push($res,$t);
					}
				}
			}
		}
		else if(strcmp($ba7erName,'majzoo2Kamel')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,5);
			if(strcmp('UU-U-',$s)==0){
				array_push($res,'مُتَفَاْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=5;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,4);
				if(strcmp('--U-',$s)==0){
					array_push($res,'مُسْتَفْعِلُنْ');
					$t=substr($chars,$i,14);
					$i+=14;
					$si+=4;
					array_push($res,$t);
				}
			}
			

			$s=substr($rokaz,$si,6);
			if(strcmp('UU-U--',$s)==0){
				array_push($res,'مُتَفَاْعِلَاْتُنْ');
				$t=substr($chars,$i,18);
				$i+=18;
				$si+=6;
				array_push($res,$t);
			}
			else if(strcmp('UU-U-U',$s)==0){
				array_push($res,'مُتَفَاْعِلَاْنْ');
				$t=substr($chars,$i,16);
				$i+=16;
				$si+=6;
				array_push($res,$t);
			}
			else
			{
				
				$s=substr($rokaz,$si,5);
				if(strcmp('UU-U-',$s)==0){
					array_push($res,'مُتَفَاْعِلُنْ');
					$t=substr($chars,$i,14);
					$i+=14;
					$si+=5;
					array_push($res,$t);
				}
				else
				{
					$s=substr($rokaz,$si,4);
					if(strcmp('UU--',$s)==0){
						array_push($res,'مُتَفَاْعِلْ');
						$t=substr($chars,$i,12);
						$i+=12;
						$si+=4;
						array_push($res,$t);
					}
					if(strcmp('--U-',$s)==0){
						array_push($res,'مُسْتَفْعِلُنْ');
						$t=substr($chars,$i,14);
						$i+=14;
						$si+=4;
						array_push($res,$t);
					}
				}
			}
		}
		else if(strcmp($ba7erName,'majzoo2Ramal')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('-U--',$s)==0){
				array_push($res,'فَاْعِلَاْتُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UU--',$s)==0){
				array_push($res,'فَعِلَاْتُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,5);
			if(strcmp('-U--U',$s)==0){
				array_push($res,'فَاْعِلَاْتَاْنْ');
				$t=substr($chars,$i,16);
				$i+=16;
				$si+=5;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,4);
				if(strcmp('-U--',$s)==0){
					array_push($res,'فَاْعِلَاْتُنْ');
					$t=substr($chars,$i,14);
					$i+=14;
					$si+=4;
					array_push($res,$t);
				}
				else if(strcmp('UU--',$s)==0){
					array_push($res,'فَعِلَاْتُنْ');
					$t=substr($chars,$i,12);
					$i+=12;
					$si+=4;
					array_push($res,$t);
				}
				else
				{
					$s=substr($rokaz,$si,3);
					if(strcmp('-U-',$s)==0){
						array_push($res,'فَاْعِلُنْ');
						$t=substr($chars,$i,10);
						$i+=10;
						$si+=3;
						array_push($res,$t);
					}
				}
			}
			
		}
		else if(strcmp($ba7erName,'majzoo2Saree3')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-UU-',$s)==0){
				array_push($res,'مُسْتَعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UUU-',$s)==0){
				array_push($res,'مُتَعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('-U-U',$s)==0){
				array_push($res,'فَاْعِلَاْنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,3);
				if(strcmp('-U-',$s)==0){
					array_push($res,'فَاْعِلُنْ');
					$t=substr($chars,$i,10);
					$i+=10;
					$si+=3;
					array_push($res,$t);
				}
			}
		}
		else if(strcmp($ba7erName,'majzoo2khafeef')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('-U--',$s)==0){
				array_push($res,'فَاْعِلَاْتُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UU--',$s)==0){
				array_push($res,'فَعِلَاْتُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
		}
		else if(strcmp($ba7erName,'majzoo2Munsare7')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-UU-',$s)==0){
				array_push($res,'مُسْتَعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UUU-',$s)==0){
				array_push($res,'مُتَعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('---U',$s)==0){
				array_push($res,'مَفْعُوْلَاْت');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,3);
				if(strcmp('---',$s)==0){
					array_push($res,'مَفْعُوْلُنْ');
					$t=substr($chars,$i,12);
					$i+=12;
					$si+=3;
					array_push($res,$t);
				}
			}
		}
		else if(strcmp($ba7erName,'majzoo2Mutakareb')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,3);
			if(strcmp('U--',$s)==0){
				array_push($res,'فَعُوْلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			else if(strcmp('U-U',$s)==0){
				array_push($res,'فَعُوْلُ');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,3);
			if(strcmp('U--',$s)==0){
				array_push($res,'فَعُوْلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			else if(strcmp('U-U',$s)==0){
				array_push($res,'فَعُوْلُ');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			
			
			$s=substr($rokaz,$si,3);
			if(strcmp('U--',$s)==0){
				array_push($res,'فَعُوْلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			else if(strcmp('U-U',$s)==0){
				array_push($res,'فَعُوْل');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,2);
				if(strcmp('U-',$s)==0){
					array_push($res,'فَعُوْ');
					$t=substr($chars,$i,6);
					$i+=6;
					$si+=2;
					array_push($res,$t);
				}
				else
				{
					$s=substr($rokaz,$si,1);
					if(strcmp('-',$s)==0){
						array_push($res,'فَعْ');
						$t=substr($chars,$i,4);
						$i+=4;
						$si+=1;
						array_push($res,$t);
					}
				}
			}
		}
		else if(strcmp($ba7erName,'majzoo2Mutadarak')==0)
		{	
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,3);
			if(strcmp('-U-',$s)==0){
				array_push($res,'فَاْعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			else if(strcmp('UU-',$s)==0){
				array_push($res,'فَعِلُنْ');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,2);
				if(strcmp('--',$s)==0){
					array_push($res,'فَاْلُنْ');
					$t=substr($chars,$i,8);
					$i+=8;
					$si+=2;
					array_push($res,$t);
				}
			}
			
			$s=substr($rokaz,$si,3);
			if(strcmp('-U-',$s)==0){
				array_push($res,'فَاْعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=3;
				array_push($res,$t);
			}
			else if(strcmp('UU-',$s)==0){
				array_push($res,'فَعِلُنْ');
				$t=substr($chars,$i,8);
				$i+=8;
				$si+=3;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,2);
				if(strcmp('--',$s)==0){
					array_push($res,'فَاْلُنْ');
					$t=substr($chars,$i,8);
					$i+=8;
					$si+=2;
					array_push($res,$t);
				}
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('-U-U',$s)==0){
				array_push($res,'فَاْعِلَاْن');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UU--',$s)==0){
				array_push($res,'فَعِلَاْتُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,3);
				if(strcmp('-U-',$s)==0){
					array_push($res,'فَاْعِلُنْ');
					$t=substr($chars,$i,10);
					$i+=10;
					$si+=3;
					array_push($res,$t);
				}
			}
		}
		else if(strcmp($ba7erName,'hazaj')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('U---',$s)==0){
				array_push($res,'مَفَاْعِيْلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U--U',$s)==0){
				array_push($res,'مَفَاْعِيْلُ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('U---',$s)==0){
				array_push($res,'مَفَاْعِيْلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,3);
				if(strcmp('U--',$s)==0){
					array_push($res,'فَعُوْلُنْ');
					$t=substr($chars,$i,10);
					$i+=10;
					$si+=3;
					array_push($res,$t);
				}
			}
		}
		else if(strcmp($ba7erName,'majzoo2Wafer')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,5);
			if(strcmp('U-UU-',$s)==0){
				array_push($res,'مُفَاْعِلَتُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=5;
				array_push($res,$t);
			}
			else{
				$s=substr($rokaz,$si,4);
				if(strcmp('U---',$s)==0){
					array_push($res,'مَفَاْعِيْلُنْ');
					$t=substr($chars,$i,14);
					$i+=14;
					$si+=4;
					array_push($res,$t);
				}
			}
			
			$s=substr($rokaz,$si,5);
			if(strcmp('U-UU-',$s)==0){
				array_push($res,'مُفَاْعِلَتُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=5;
				array_push($res,$t);
			}
			else{
				$s=substr($rokaz,$si,4);
				if(strcmp('U---',$s)==0){
					array_push($res,'مَفَاْعِيْلُنْ');
					$t=substr($chars,$i,14);
					$i+=14;
					$si+=4;
					array_push($res,$t);
				}
			}
		}
		else if(strcmp($ba7erName,'majzoo2Rajaz')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-UU-',$s)==0){
				array_push($res,'مُسْتَعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UUU-',$s)==0){
				array_push($res,'مُتَعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=4;
				array_push($res,$t);
			}
			
			if(strlen($rokaz)==$si+5)
			{
				$s=substr($rokaz,$si,5);
				if(strcmp('--U--',$s)==0){
					array_push($res,'مُسْتَفْعِلَاْنِ');
					$t=substr($chars,$i,16);
					$i+=16;
					$si+=5;
					array_push($res,$t);
				}
			}
			else
			{
				$s=substr($rokaz,$si,4);
				if(strcmp('--U-',$s)==0){
					array_push($res,'مُسْتَفْعِلُنْ');
					$t=substr($chars,$i,14);
					$i+=14;
					$si+=4;
					array_push($res,$t);
				}
				else if(strcmp('U-U-',$s)==0){
					array_push($res,'مُتَفْعِلُنْ');
					$t=substr($chars,$i,12);
					$i+=12;
					$si+=4;
					array_push($res,$t);
				}
				else if(strcmp('-UU-',$s)==0){
					array_push($res,'مُسْتَعِلُنْ');
					$t=substr($chars,$i,12);
					$i+=12;
					$si+=4;
					array_push($res,$t);
				}
				else if(strcmp('UUU-',$s)==0){
					array_push($res,'مُتَعِلُنْ');
					$t=substr($chars,$i,10);
					$i+=10;
					$si+=4;
					array_push($res,$t);
				}
				else
				{
					$s=substr($rokaz,$si,3);
					if(strcmp('---',$s)==0){
						array_push($res,'مُسْتَفْعِلْ');
						$t=substr($chars,$i,12);
						$i+=12;
						$si+=3;
						array_push($res,$t);
					}
				}
			}
		}
		else if(strcmp($ba7erName,'modare3')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('U--U',$s)==0){
				array_push($res,'مَفَاْعِيْلُ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مَفَاْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('-U--',$s)==0){
				array_push($res,'فَاْعِلَاْتُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
		}
		else if(strcmp($ba7erName,'moktadab')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('-U-U',$s)==0){
				array_push($res,'فَاْعِلَاْتُ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('-UU-',$s)==0){
				array_push($res,'مُسْتَعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
		}
		else if(strcmp($ba7erName,'mojtath')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			
			$s=substr($rokaz,$si,4);
			if(strcmp('-U--',$s)==0){
				array_push($res,'فَاْعِلَاْتُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UU--',$s)==0){
				array_push($res,'فَعِلَاْتُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,3);
				if(strcmp('---',$s)==0){
					array_push($res,'فَاْلَاْتُنْ');
					$t=substr($chars,$i,12);
					$i+=12;
					$si+=3;
					array_push($res,$t);
				}
			}
		}
		else if(strcmp($ba7erName,'manhookRajaz')==0)
		{
			$i=0;
			$si=0;
			$s=substr($rokaz,$si,4);
			if(strcmp('--U-',$s)==0){
				array_push($res,'مُسْتَفْعِلُنْ');
				$t=substr($chars,$i,14);
				$i+=14;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('U-U-',$s)==0){
				array_push($res,'مُتَفْعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('-UU-',$s)==0){
				array_push($res,'مُسْتَعِلُنْ');
				$t=substr($chars,$i,12);
				$i+=12;
				$si+=4;
				array_push($res,$t);
			}
			else if(strcmp('UUU-',$s)==0){
				array_push($res,'مُتَعِلُنْ');
				$t=substr($chars,$i,10);
				$i+=10;
				$si+=4;
				array_push($res,$t);
			}
			else
			{
				$s=substr($rokaz,$si,3);
				if(strcmp('---',$s)==0){
					array_push($res,'مُسْتَفْعِلْ');
					$t=substr($chars,$i,12);
					$i+=12;
					$si+=3;
					array_push($res,$t);
				}
			}
		}
		
		return $res;
	}
	

	
	
	
	
	
	
	/* new eshbaa3 algorithm area !!*/
	
	// توليد جدول الحقيقة لعدد متغيرات معين
	// عدد النتائج الكلية = 2 مرفوعة للقوة عدد المتغيرات
	private function _getTruthValues($count) {
		/*if($count <=0)
		{
			return array();
		}*/
		if ($count === 1) {
			return array(array('1'), array('0'));
		}   
		$trues = $falses = self::_getTruthValues(--$count);
		for ($i = 0, $total = count($trues); $i < $total; $i++) {
			array_unshift($trues[$i], '1');
			array_unshift($falses[$i], '0');
		}   
		return array_merge($trues, $falses);
	}
	
	
	// new eshaa3 method !!!
	// brute force approach
	// خوارزميّة الإشباع الجديدة التي تداركت ثغرات الخوارزميّة القديمة بشكل كامل و دقيق
	private function _doEshbaa3Shater($t){
		$t='#'.$t.'#';
		
		// تحديد كل كلمة تنتهي بضمير يُشبَعُ !!
		$strArr=preg_split('/(هُ|هِ|مُ)#/',$t,-1,PREG_SPLIT_DELIM_CAPTURE);
		$pos=array();
		for($i=0;$i<count($strArr);$i++)
		{
			if(strcmp($strArr[$i],'هُ')==0 || strcmp($strArr[$i],'هِ')==0 || strcmp($strArr[$i],'مُ')==0)
			{
				array_push($pos,$i);
			}
		}
		
		if(count($pos)>0)
		{
			$truth=self::_getTruthValues(count($pos));
		}
		else
		{
			$truth=array(array(0));
		}
		
		
		
		foreach($truth as $state)
		{
			$tmpArr=$strArr;
			$tmpPos=$pos;
			for($i=0;$i<count($state);$i++)
			{
				if(strcmp($state[$i],'1')==0)
				{
					//أشبع
					if(strcmp($tmpArr[$tmpPos[$i]],'هُ')==0)
					{
						$tmpArr[$tmpPos[$i]].='وْ';
					}
					else if(strcmp($tmpArr[$tmpPos[$i]],'هِ')==0)
					{
						$tmpArr[$tmpPos[$i]].='يْ';
					}
					else if(strcmp($tmpArr[$tmpPos[$i]],'مُ')==0)
					{
						$tmpArr[$tmpPos[$i]].='وْ';
					}
				}
			}
			$stateText=implode($tmpArr);
			$t=$stateText;
			
			while(strstr('##',$t))
			{
				$t=str_replace('##','#',$t);
			}
			
			//إذا الحالة المشبعة موزونة أعدها
			/* عملية تقطيع النص و محاولة استخلاص البحر */
			$t=str_replace('#',' ',$t);
			while(strstr(' ',$t))
			{
				$t=str_replace(' ','',$t);
			}
			$arrodiWritten=$t;
			$chars=self::_getCharsOnly($arrodiWritten);
			$harakat=self::_getHarakatOnly($arrodiWritten);
			$rokaz=self::_rokzahKhoutayt($harakat);
			$ba7erName=self::_getBa7er($rokaz);
			
			if(strcmp($ba7erName,'unknown')!=0)
			{
				$tafa3eel=self::_getTafa3eel($rokaz,$chars,$ba7erName);
				$result=array("shater"=>$t,"arrodi"=>$arrodiWritten,"chars"=>$chars,"harakat"=>$harakat,"rokaz"=>$rokaz,'ba7erName'=>$ba7erName,'tafa3eel'=>$tafa3eel);
				return $result;
			}
		}
		
		// الشطر غير موزون رغم الإشباعات !!!
		
		return 'unknownAlso';
		
	}
	/* end new eshbaa3 area */
	

	
	

	
	// تحديد التفعيلة الغالبة على القصيدة التفعيلية لاعتبارها هي الأساس في التقطيع
	private function _whatTafeelaPoemOn($t){
		$oldT=$t;
		if(strlen($t)>=4)
		{
			$t=substr($t,0,4);
		}
		
		if(strcmp($t,'UUU-')==0)
		{
			// rajaz
			$tafa3eelBase=array('rajaz'=>'/(--U-|-UU-|U-U-|UUU-|U-){5}/','khabab'=>'/(UU-|-UU|--){7}/');
		}
		else if(strcmp($t,'UU-U')==0)
		{
			// mutadarak - ramal - kamel
			$tafa3eelBase=array('kamel'=>'/(UU-U-|--U-){4}/','ramal'=>'/(-U--|UU--|UU-U){5}/','mutadarak'=>'/(-U-|UU-){7}/');
		}
		else if(strcmp($t,'UU--')==0)
		{
			// ramal 
			$tafa3eelBase=array('ramal'=>'/(-U--|UU--|UU-U){5}/');
		}
		else if(strcmp($t,'U-UU')==0)
		{
			// mutakareb - wafer
			$tafa3eelBase=array('wafer'=>'/(U-UU-|U---){4}/','mutakareb'=>'/(U--|U-U|U-){7}/');
		}
		else if(strcmp($t,'U-U-')==0)
		{
			// mutakareb - rajaz
			$tafa3eelBase=array('rajaz'=>'/(--U-|-UU-|U-U-|UUU-|U-){5}/','mutakareb'=>'/(U--|U-U|U-){7}/');
		}
		else if(strcmp($t,'U--U')==0)
		{
			// mutakareb - hazaj
			$tafa3eelBase=array('wafer'=>'/(U-UU-|U---){4}/','mutakareb'=>'/(U--|U-U|U-){7}/');
		}
		else if(strcmp($t,'U---')==0)
		{
			// wafer - hazaj
			$tafa3eelBase=array('wafer'=>'/(U-UU-|U---)/');
		}
		else if(strcmp($t,'-UU-')==0)
		{
			// rajaz 
			$tafa3eelBase=array('rajaz'=>'/(--U-|-UU-|U-U-|UUU-|U-){5}/');
		}
		else if(strcmp($t,'-U-U')==0)
		{
			// mutadarak
			$tafa3eelBase=array('mutadarak'=>'/(-U-|UU-){7}/');
		}
		else if(strcmp($t,'-U--')==0)
		{
			// mutadarak - ramal 
			$tafa3eelBase=array('ramal'=>'/(-U--|UU--|UU-U){5}/','mutadarak'=>'/(-U-|UU-){7}/');
		}
		else if(strcmp($t,'--U-')==0)
		{
			// kamel - mutadarak - rajaz
			$tafa3eelBase=array('kamel'=>'/(UU-U-|--U-){4}/','rajaz'=>'/(--U-|-UU-|U-U-|UUU-|U-){5}/','mutadarak'=>'/(-U-|UU-){7}/');
		}
		else
		{
			return 'unknown';
		}
		
		
		
		
		
		$t=$oldT;
		if(strlen($t)>=21)
		{
			$t=substr($t,0,21);
		//	$tafa3eelBase=array('kamel'=>'/(UU-U-|--U-){4}/','wafer'=>'/(U-UU-|U---){4}/','ramal'=>'/(-U--|UU--|UU-U){5}/','rajaz'=>'/(--U-|-UU-|U-U-|UUU-|U-){5}/','mutadarak'=>'/(-U-|UU-){7}/','mutakareb'=>'/(U--|U-U|U-){7}/');
		}
		$max=0;
		$ba7er='unknown';
		foreach($tafa3eelBase as $ba7eri=>$tafeelai)
		{
			$i=preg_match_all($tafeelai,$t,$matches);
			if($i>$max)
			{
				$max=$i;
				$ba7er=$ba7eri;
				
				if(strcmp($ba7er,'wafer')==0)
				{
					$ba7er='hazaj';
					foreach($matches as $mm)
					{
						foreach($mm as $m)
						{
							if(strcmp($m,'U-UU-')==0)
							{
								$ba7er='wafer';
							}
						}
					}
				}
				else if(strcmp($ba7er,'rajaz')==0)
				{
					foreach($matches as $mm)
					{
						foreach($mm as $m)
						{
							if(strcmp($m,'U-UU-')==0)
							{
								$ba7er='kamel';
							}
						}
					}
				}
				/*else if(strcmp($ba7er,'khabab')==0)
				{
					foreach($matches as $mm)
					{
						foreach($mm as $m)
						{
							if(strcmp($m,'-U-')==0)
							{
								$ba7er='mutadarak';
							}
						}
					}
				}*/
				//var_dump($max);
				//var_dump($ba7er);
			}
		}
		//var_dump($ba7er);
		return $ba7er;
	}
	
	// إعاداة التفعيلات الخاصة بقصيدة التفعيلة مع الجزء المساوي لكل تفعيلة من النص
	private function _getTafaeelForTafeelaPoem($ba7erName,$rokaz,$chars){
		
		$t=$rokaz;
		$strArr=array();
		$namesArr=array();
		//var_dump($ba7erName);
		
		if(strcmp($ba7erName,'unknown')==0)
		{
			return 'unknown';
		}
		
		if(strcmp($ba7erName,'kamel')==0)
		{
			//$strArr=preg_split('/(UU-U-U|UU-U-|--U-)/',$t,-1,PREG_SPLIT_DELIM_CAPTURE);
			$len=(int)round(strlen($t)/4);
			for($i=0;$i<$len+1;$i++)
			{
				if(strcmp(substr($t,0,5),'UU-U-')==0)
				{
					$t=substr($t,5,strlen($t)-5);
					$name='مُتَفَاْعِلُنْ';
					array_push($strArr,'UU-U-');
					array_push($namesArr,$name);
				}
				else if(strcmp(substr($t,0,4),'--U-')==0)
				{
					$t=substr($t,4,strlen($t)-4);
					$name='مُسْتَفْعِلُنْ';
					array_push($strArr,'--U-');
					array_push($namesArr,$name);
				}
				else if(strlen($t)>=4)
				{
					$name='????';
					array_push($strArr,substr($t,0,1));
					array_push($namesArr,$name);
					$t=substr($t,1,strlen($t)-1);
				}
				else if(strlen($t)<4)
				{
					
					$name='????';
					array_push($strArr,$t);
					if(strlen($t)!=0)
					{
						array_push($namesArr,$name);
					}
					$i+=$len; // exit from loop !!!
				}
			}
		}
		else if(strcmp($ba7erName,'wafer')==0)
		{
			//$strArr=preg_split('/(UU-U-|U---)/',$t,-1,PREG_SPLIT_DELIM_CAPTURE);
			$len=(int)round(strlen($t)/4);
			for($i=0;$i<$len+1;$i++)
			{
				if(strcmp(substr($t,0,5),'U-UU-')==0)
				{
					$t=substr($t,5,strlen($t)-5);
					$name='مُفَاْعِلَتُنْ';
					array_push($strArr,'U-UU-');
					array_push($namesArr,$name);
				}
				else if(strcmp(substr($t,0,4),'U---')==0)
				{
					$t=substr($t,4,strlen($t)-4);
					$name='مَفَاْعِيْلُنْ';
					array_push($strArr,'U---');
					array_push($namesArr,$name);
				}
				else if(strlen($t)>4)
				{
					$name='????';
					array_push($strArr,substr($t,0,1));
					array_push($namesArr,$name);
					$t=substr($t,1,strlen($t)-1);
				}
				else if(strlen($t)<4)
				{
					
					$name='????';
					array_push($strArr,$t);
					if(strlen($t)!=0)
					{
						array_push($namesArr,$name);
					}
					$i+=$len; // exit from loop !!!
				}
			}
		}
		else if(strcmp($ba7erName,'ramal')==0)
		{
			//$strArr=preg_split('/(-U--|UU--|UU-U)/',$t,-1,PREG_SPLIT_DELIM_CAPTURE);
			$len=(int)round(strlen($t)/4);
			for($i=0;$i<$len+1;$i++)
			{
				if(strlen($t)>=4)
				{
					$s=substr($t,0,4);
					$t=substr($t,4,strlen($t)-4);
					if(strcmp($s,'-U--')==0)
					{
						$name='فَاْعِلَاْتُنْ';
					}
					else if(strcmp($s,'UU--')==0)
					{
						$name='فَعِلَاْتُنْ';
					}
					else if(strcmp($s,'UU-U')==0)
					{
						$name='فَعِلَاْتُ';
					}
					else
					{
						$name='????';
					}
					array_push($strArr,$s);
					array_push($namesArr,$name);
				}
				else
				{
					
					$name='????';
					array_push($strArr,$t);
					if(strlen($t)!=0)
					{
						array_push($namesArr,$name);
					}
					$i+=$len; // exit from loop !!!
				}
			}
		}
		else if(strcmp($ba7erName,'mutakareb')==0)
		{
			$len=(int)round(strlen($t)/3);
			for($i=0;$i<$len+1;$i++)
			{
				if(strlen($t)>=3)
				{
					$s=substr($t,0,3);
					$t=substr($t,3,strlen($t)-3);
					if(strcmp($s,'U--')==0)
					{
						$name='فَعُوْلُنْ';
					}
					else if(strcmp($s,'U-U')==0)
					{
						$name='فَعُوْلُ';
					}
					else
					{
						$name='????';
					}
					array_push($strArr,$s);
					array_push($namesArr,$name);
				}
				else
				{
					if(strcmp($t,'U-')==0)
					{
						$name='فَعُوْ';
					}
					else
					{
						$name='????';
					}
					array_push($strArr,$t);
					if(strlen($t)!=0)
					{
						array_push($namesArr,$name);
					}
					$i+=$len; // exit from loop !!!
				}
			}
		}
		else if(strcmp($ba7erName,'mutadarak')==0)
		{
			//$strArr=preg_split('/(-U-|UU-)/',$t,-1,PREG_SPLIT_DELIM_CAPTURE);
			$len=(int)round(strlen($t)/3);
			for($i=0;$i<$len+1;$i++)
			{
				if(strlen($t)>=3)
				{
					$s=substr($t,0,3);
					$t=substr($t,3,strlen($t)-3);
					if(strcmp($s,'-U-')==0)
					{
						$name='فَاْعِلُنْ';
					}
					else if(strcmp($s,'UU-')==0)
					{
						$name='فَعِلُنْ';
					}
					else
					{
						$name='????';
					}
					array_push($strArr,$s);
					array_push($namesArr,$name);
				}
				else
				{
					$name='????';
					array_push($strArr,$t);
					if(strlen($t)!=0)
					{
						array_push($namesArr,$name);
					}
					$i+=$len; // exit from loop !!!
				}
			}
		}
		else if(strcmp($ba7erName,'hazaj')==0)
		{
			//$strArr=preg_split('/(U---|U--U)/',$t,-1,PREG_SPLIT_DELIM_CAPTURE);
			$len=(int)round(strlen($t)/4);
			for($i=0;$i<$len+1;$i++)
			{
				if(strlen($t)>=4)
				{
					$s=substr($t,0,4);
					$t=substr($t,4,strlen($t)-4);
					if(strcmp($s,'U---')==0)
					{
						$name='مَفَاْعِيْلُنْ';
					}
					else if(strcmp($s,'U--U')==0)
					{
						$name='مَفَاْعِيْلُ';
					}
					else
					{
						$name='????';
					}
					array_push($strArr,$s);
					array_push($namesArr,$name);
				}
				else
				{
					
					$name='????';
					array_push($strArr,$t);
					if(strlen($t)!=0)
					{
						array_push($namesArr,$name);
					}
					$i+=$len; // exit from loop !!!
				}
			}
		}
		else if(strcmp($ba7erName,'rajaz')==0)
		{
			//$strArr=preg_split('/(--U-|-UU-|U-U-|UUU-|U-U|U-)/',$t,-1,PREG_SPLIT_DELIM_CAPTURE);
			$len=(int)round(strlen($t)/4);
			for($i=0;$i<$len+1;$i++)
			{
				if(strlen($t)>=4)
				{
					$s=substr($t,0,4);
					$t=substr($t,4,strlen($t)-4);
					if(strcmp($s,'--U-')==0)
					{
						$name='مُسْتَفْعِلُنْ';
					}
					else if(strcmp($s,'-UU-')==0)
					{
						$name='مُسْتَعِلُنْ';
					}
					else if(strcmp($s,'U-U-')==0)
					{
						$name='مُتَفْعِلُنْ';
					}
					else if(strcmp($s,'UUU-')==0)
					{
						$name='مُتَعِلُنْ';
					}
					else if(strcmp($s,'U---')==0)
					{
						$name='فَعُوْ';
						$t=substr($s,2,4).$t;
						$s=substr($s,0,2);
					}
					else
					{
						$name='????';
					}
					array_push($strArr,$s);
					array_push($namesArr,$name);
				}
				else
				{
					
					if(strcmp($t,'U-U')==0)
					{
						$name='فَعُوْل';
					}
					else if(strcmp($t,'U-')==0)
					{
						$name='فَعُوْ';
					}
					else
					{
						$name='????';
					}
					array_push($strArr,$t);
					if(strlen($t)!=0)
					{
						array_push($namesArr,$name);
					}
					$i+=$len; // exit from loop !!!
				}
			}
		}
		
		$tafa3eelArr=array();
		$charsArr=array();
		$i=0;
		foreach($strArr as $t)
		{
			if(strcmp($t,'')!=0)
			{
				array_push($tafa3eelArr,$t);
				$t=preg_split('//',$t);
				//calculate lenth of word
				$currentLen=0;
				for($l=0;$l<count($t);$l++)
				{
					if(strcmp($t[$l],'-')==0)
					{
						$currentLen+=2;
					}
					else if(strcmp($t[$l],'U')==0)
					{
						$currentLen++;
					}
				}
				$currentLen=$currentLen*2;
				//var_dump($i);
				//var_dump($currentLen);
				$x=substr($chars,$i,$currentLen);
				$i+=$currentLen;
				array_push($charsArr,$x);
			}
		}
		
		
		for($i=0;$i<count($charsArr);$i++) // تضبيط عرض الألف المقصورة في النتائج كي لا تظهر متصلة !!
		{
			$charsArr[$i]=str_replace('ى','ى ',$charsArr[$i]);
		}
		return (array('ba7er'=>$ba7erName,'tafa3eel'=>$tafa3eelArr,'names'=>$namesArr,'words'=>$charsArr));
		
	}
	
	// تحليل قافية بيت
	private function _analyseQafeah($ajez){
		$currentAjez=$ajez;
		/* عملية تحويل العجز إلى كتابة عروضيّة */
		$currentAjez=self::_specialCases($currentAjez); // clean text in embedded here !!
		$currentAjez=self::_moonySunnyLam($currentAjez);
		$currentAjez=self::_tanweenShaddeh($currentAjez,true);
		$currentAjez=self::_hamzetWasel($currentAjez);
		$currentAjez=str_replace('#',' ',$currentAjez);
		while(strstr(' ',$currentAjez))
		{
			$t=str_replace(' ','',$currentAjez);
		}
		/* نهاية تحويل العجز إلى كتابة عروضيّة */
		$currentAjez=self::_str2chars($currentAjez);
		$currentQafeah=array();
		$sokonsCount=0;
		//تحديد القافية بين آخر سكونين في البيت و الحرف الذي قبل السكون
		for($i=count($currentAjez)-1;$i>=0;$i--)
		{
			array_push($currentQafeah,$currentAjez[$i]);
			if(strcmp($currentAjez[$i],'ْ')==0 || 
			   (strcmp($currentAjez[$i],'ا')==0 && strcmp($currentAjez[$i+1],'ْ')!=0) ||
			   (strcmp($currentAjez[$i],'ى')==0 && strcmp($currentAjez[$i+1],'ْ')!=0))
			{
				$sokonsCount++;
			}
			if($sokonsCount>=2)
			{
				if(array_key_exists($i-1,$currentAjez))
					array_push($currentQafeah,$currentAjez[$i-1]);
				$index=$i-2;
				do
				{
					if(array_key_exists($index,$currentAjez))
					{
						array_push($currentQafeah,$currentAjez[$index]);
					}
					$index--;
				}while(in_array($currentAjez[$index],self::$alphabet)===false);
				if(strcmp($currentQafeah[count($currentQafeah)-3],'ْ')==0)
				{
					if(array_key_exists($index,$currentAjez))
					{
						array_push($currentQafeah,$currentAjez[$index]);
					}
				}
				break;
			}
		}
		$currentQafeah=implode(array_reverse($currentQafeah,false));
		$currentQafeah=str_replace('#',' ',$currentQafeah);
		
		//var_dump($currentQafeah);
		//تحليل القافية
		$currentQafeahDetails=array(); // القافية الحالية
		$currentQafeahAlphas=array(); //حروف القافية الحالية
		$currentQafeahHarakat=array(); // حركات حروف القافية الحالية
		$currentQafeahWhatWordIs=array(); // كل حرف في أي كلمة الأولى أم الثانية ... هام من أجل ألف التأسيس لأنها يجب ألا تكون في كلمة منفصلة عن كلمة الروي
		$wordNo=1;
		$t=self::_str2chars($currentQafeah);
		for($i=count($t)-1;$i>=0;$i--)
		{
			if(in_array($t[$i],self::$alphabet)===true)
			{
				if(strcmp($t[$i],'#')==0)
				{
					$wordNo++;
				}
				array_push($currentQafeahAlphas,$t[$i]);
				array_push($currentQafeahHarakat,'');
				array_push($currentQafeahWhatWordIs,$wordNo);
			}
			else if(in_array($t[$i],self::$harakat)===true)
			{
				if(array_key_exists($i-1,$t))
				{
					if(strcmp($t[$i],'#')==0)
					{
						$wordNo++;
					}
					array_push($currentQafeahAlphas,$t[$i-1]);
					array_push($currentQafeahWhatWordIs,$wordNo);
				}
				else
				{
					array_push($currentQafeahAlphas,'');
					array_push($currentQafeahWhatWordIs,$wordNo);
				}
				array_push($currentQafeahHarakat,$t[$i]);
				$i--;
			}
		}
		
		$currentQafeahAlphas=array_reverse($currentQafeahAlphas,false);
		$currentQafeahHarakat=array_reverse($currentQafeahHarakat,false);
		$currentQafeahWhatWordIs=array_reverse($currentQafeahWhatWordIs,false);
		$currentRaweePos=-1;
		
		
		/*var_dump($currentQafeahAlphas);
		var_dump($currentQafeahWhatWordIs);
		var_dump($currentQafeahHarakat);*/
		
		
		// نص القافية
		$currentQafeahDetails['text']='';
		if(strcmp($currentQafeahAlphas[0],'')==0)
		{
			for($i=1;$i<count($currentQafeahAlphas);$i++)
			{
				$currentQafeahDetails['text'].=$currentQafeahAlphas[$i].$currentQafeahHarakat[$i];
			}
		}
		else
		{
			for($i=0;$i<count($currentQafeahAlphas);$i++)
			{
				$currentQafeahDetails['text'].=$currentQafeahAlphas[$i].$currentQafeahHarakat[$i];
			}
		}
		//الروي و الوصل و الخروج
		$currentQafeahDetails['rawee']='';
		$currentQafeahDetails['wasel']='';
		$currentQafeahDetails['kharoog']='';
		if(strcmp($currentQafeahAlphas[count($currentQafeahAlphas)-1],'ه')==0 && // الحرف الأخير هاء ساكنة و ما قبله ليس ساكناً
		   strcmp($currentQafeahHarakat[count($currentQafeahHarakat)-2],'ْ')!=0)
		{
			$currentQafeahDetails['type']='F';// يعني قافية مطلقة
			$currentRaweePos=count($currentQafeahAlphas)-2;
			$currentQafeahDetails['rawee']=$currentQafeahAlphas[count($currentQafeahAlphas)-2].$currentQafeahHarakat[count($currentQafeahHarakat)-2];
			$currentQafeahDetails['wasel']=$currentQafeahAlphas[count($currentQafeahAlphas)-1].$currentQafeahHarakat[count($currentQafeahHarakat)-1];
		}
		else if(strcmp($currentQafeahAlphas[count($currentQafeahAlphas)-1],'ك')==0 && // الحرف الأخير كاف ساكنة و ما قبله ليس ساكناً
		   strcmp($currentQafeahHarakat[count($currentQafeahHarakat)-2],'ْ')!=0)
		{
			$currentQafeahDetails['type']='F';// يعني قافية مطلقة
			$currentRaweePos=count($currentQafeahAlphas)-2;
			$currentQafeahDetails['rawee']=$currentQafeahAlphas[count($currentQafeahAlphas)-2].$currentQafeahHarakat[count($currentQafeahHarakat)-2];
			$currentQafeahDetails['wasel']=$currentQafeahAlphas[count($currentQafeahAlphas)-1].$currentQafeahHarakat[count($currentQafeahHarakat)-1];
		}
		else if(strcmp($currentQafeahAlphas[count($currentQafeahAlphas)-1],'ا')==0 ||
				strcmp($currentQafeahAlphas[count($currentQafeahAlphas)-1],'ى')==0 ||		// الحرف الأخير ألف أو واو أو ياء
		        strcmp($currentQafeahAlphas[count($currentQafeahAlphas)-1],'و')==0 ||
				strcmp($currentQafeahAlphas[count($currentQafeahAlphas)-1],'ي')==0)
		{
			if(strcmp($currentQafeahAlphas[count($currentQafeahAlphas)-2],'ه')==0 && strcmp($currentQafeahHarakat[count($currentQafeahHarakat)-2],'ْ')!=0) // قبلها هاء متحركة بحركة مناسبة
			{
				
				$currentQafeahDetails['type']='F';
				$currentRaweePos=count($currentQafeahAlphas)-3;
				$currentQafeahDetails['rawee']=$currentQafeahAlphas[count($currentQafeahAlphas)-3].$currentQafeahHarakat[count($currentQafeahHarakat)-3];
				$currentQafeahDetails['wasel']=$currentQafeahAlphas[count($currentQafeahAlphas)-2].$currentQafeahHarakat[count($currentQafeahHarakat)-2];
				$currentQafeahDetails['kharoog']=$currentQafeahAlphas[count($currentQafeahAlphas)-1].$currentQafeahHarakat[count($currentQafeahHarakat)-1];

			}
			else
			{
				$currentQafeahDetails['type']='F';
				$currentRaweePos=count($currentQafeahAlphas)-2;
				$currentQafeahDetails['rawee']=$currentQafeahAlphas[count($currentQafeahAlphas)-2].$currentQafeahHarakat[count($currentQafeahHarakat)-2];
				$currentQafeahDetails['wasel']=$currentQafeahAlphas[count($currentQafeahAlphas)-1].$currentQafeahHarakat[count($currentQafeahHarakat)-1];
			}
		}
		else
		{
			$currentQafeahDetails['type']='M'; // مقيّدة
			$currentRaweePos=count($currentQafeahAlphas)-1;
			$currentQafeahDetails['rawee']=$currentQafeahAlphas[count($currentQafeahAlphas)-1].$currentQafeahHarakat[count($currentQafeahHarakat)-1];
		}
		
		//الردف و التأسيس
		//var_dump($currentRaweePos);
		$currentQafeahDetails['ta2ses']='';
		$currentQafeahDetails['dakheel']='';
		$currentQafeahDetails['redf']='';
		$c=$currentQafeahAlphas[$currentRaweePos-1];
		$ch=$currentQafeahHarakat[$currentRaweePos-1];
		$cw=$currentQafeahWhatWordIs[$currentRaweePos-1];
		if($currentRaweePos>1)
		{
			if(strcmp($currentQafeahAlphas[$currentRaweePos-2],'#')!=0)
			{
				$cb=$currentQafeahAlphas[$currentRaweePos-2];
				$cbh=$currentQafeahHarakat[$currentRaweePos-2];
				$cbw=$currentQafeahWhatWordIs[$currentRaweePos-2];
			}
			else
			{
				if(array_key_exists($currentRaweePos-3,$currentQafeahAlphas))
				{
					$cb=$currentQafeahAlphas[$currentRaweePos-3];
					$cbh=$currentQafeahHarakat[$currentRaweePos-3];
					$cbw=$currentQafeahWhatWordIs[$currentRaweePos-3];
				}
			}
		}
		else
		{
			$cb='غ'; // أي حرف عدا ألف التأسيس كي لا يتحقق الشرط الثاني التالي
			$cbh='غ';
			$cbw=-1;
		}
		
		if ((strcmp($c,'و')==0 && strcmp($ch,'ْ')==0 && strcmp($cbh,'ُ')==0) ||
			(strcmp($c,'ي')==0 && strcmp($ch,'ْ')==0 && strcmp($cbh,'ِ')==0) ||
			(strcmp($c,'ا')==0 && strcmp($cbh,'َ')==0) ||
			(strcmp($c,'ى')==0 && strcmp($cbh,'َ')==0))
		{
			$currentQafeahDetails['redf']=$c.$ch;
		}
		else if((strcmp($cb,'ا')==0 || strcmp($cb,'ى')==0) && $cbw==1)
		{
			$currentQafeahDetails['ta2ses']=$cb;
			$currentQafeahDetails['dakheel']=$c.$ch;
		}
		
		// تحديد نوع القافية
		if(strcmp($currentQafeahDetails['type'],'F')==0)
		{
			if(strcmp($currentQafeahDetails['kharoog'],'')==0 && strcmp($currentQafeahDetails['redf'],'')==0 && strcmp($currentQafeahDetails['ta2ses'],'')==0)
			{
				$currentQafeahDetails['type']='قافية مطلقة مجرَّدة';
			}
			else if(strcmp($currentQafeahDetails['redf'],'')!=0)
			{
				$currentQafeahDetails['type']='قافية مطلقة بردف';
				if(strcmp($currentQafeahDetails['kharoog'],'')!=0)
				{
					$currentQafeahDetails['type'].=' و خروج';
				}
			}
			else if(strcmp($currentQafeahDetails['ta2ses'],'')!=0)
			{
				$currentQafeahDetails['type']='قافية مطلقة بتأسيس';
				if(strcmp($currentQafeahDetails['kharoog'],'')!=0)
				{
					$currentQafeahDetails['type'].=' و خروج';
				}
			}
			else if(strcmp($currentQafeahDetails['kharoog'],'')!=0)
			{
				$currentQafeahDetails['type']='قافية مطلقة بخروج';
			}
		}
		else if(strcmp($currentQafeahDetails['type'],'M')==0)
		{
			if(strcmp($currentQafeahDetails['redf'],'')==0 && strcmp($currentQafeahDetails['ta2ses'],'')==0)
			{
				$currentQafeahDetails['type']='قافية مقيّدة مجرَّدة';
			}
			else if(strcmp($currentQafeahDetails['redf'],'')!=0)
			{
				$currentQafeahDetails['type']='قافية مقيّدة بردف';
			}
			else
			{
				$currentQafeahDetails['type']='قافية مقيّدة بتأسيس';
			}
		}
		
		$currentQafeahDetails['text']=str_replace('#',' ',$currentQafeahDetails['text']);
		return $currentQafeahDetails;
	}
	
	// إعادة رقم الحرف كتابة
	private function _getCharName($n)
	{
		if($n==1)
		{
			$t='الأوّل';
		}
		else if($n==2)
		{
			$t='الثّاني';
		}
		else if($n==3)
		{
			$t='الثّالث';
		}
		else if($n==4)
		{
			$t='الرّابع';
		}
		else if($n==5)
		{
			$t='الخامس';
		}
		else if($n==6)
		{
			$t='السّادس';
		}
		else if($n==7)
		{
			$t='السّابع';
		}
		else if($n==8)
		{
			$t='الثّامن';
		}
		else if($n==9)
		{
			$t='التّاسع';
		}
		else if($n==10)
		{
			$t='العاشر';
		}
		else
		{
			$t='رقم '.$n;
		}
		return $t;
	}
	
	// إعادة رقم حالة التفعيلة
	private function _getStateName($n)
	{
		if($n==1)
		{
			$t='الأولى';
		}
		else if($n==2)
		{
			$t='الثّانية';
		}
		else if($n==3)
		{
			$t='الثّالثة';
		}
		else if($n==4)
		{
			$t='الرّابعة';
		}
		else if($n==5)
		{
			$t='الخامسة';
		}
		else if($n==6)
		{
			$t='السّادسة';
		}
		else
		{
			$t='رقم '.$n;
		}
		return $t;
	}
	
	// مقارنة تقطيع بحالات تفعيلة ما
	private function _compareWithTafeela($t,$mustBeArr,$mustBeNames)
	{
		/*$max=$mustBe[0];
		$m=0;
		foreach($mustBe as $a)
		{
			if(similar_text($a,$t)>$m)
			{
				$m=similar_text($a,$t);
				$max=$a;
			}
		}
		$mustBe=$max;
		*/
		$errs=array();
		$stateNo=0;
		for($indexZ=0;$indexZ<count($mustBeArr);$indexZ++)
		{
			$mustBe=$mustBeArr[$indexZ];
			$mustBeName=$mustBeNames[$indexZ];
			$stateNo++;
			if(strlen($mustBe)>=strlen($t)) //الحالة المدخلة بنفس طول التفعيلة الأقرب أو أقصر منها 
			{
				$charsT=str_split($t);
				$charsMustBe=str_split($mustBe);
				for($i=0,$len=0;$i<count($charsT);$i++)
				{
					if(strcmp($charsT[$i],'U')==0)
					{
						$len++;
					}
					else if(strcmp($charsT[$i],'-')==0)
					{
						$len++;
						$len++;
					}
					if(strcmp($charsT[$i],$charsMustBe[$i])==0)
					{
						continue;
					}
					if(strcmp($charsT[$i],'U')==0)
					{
						array_push($errs,'<b> الصورة'.self::_getStateName($stateNo).' ('.$mustBeName.') :</b>يجب تسكين الحرف '.self::_getCharName($len+1).' كي نحصل على تقطيع متوافق مع هذه الصورة');
						break;
					}
					if(strcmp($charsT[$i],'-')==0)
					{
						array_push($errs,'<b> الصورة'.self::_getStateName($stateNo).' ('.$mustBeName.') :</b>يجب أن يكون الحرف '.self::_getCharName($len).' متحركاً كي نحصل على تقطيع متوافق مع هذه الصورة');
						break;
					}
					if($i==count($charsT)-1)
					{
						array_push($errs,'<b> الصورة'.self::_getStateName($stateNo).' ('.$mustBeName.') :</b>التقطيع الحالي لهذه التفعيلة أقصر وزنيّاً من هذه الصورة');
						break;
					}
				}
			}
			else if(strlen($mustBe)<strlen($t))
			{
				$charsT=str_split($t);
				$charsMustBe=str_split($mustBe);
				for($i=0;$i<count($charsMustBe);$i++)
				{
					if(strcmp($charsT[$i],'U')==0)
					{
						$len++;
					}
					else if(strcmp($charsT[$i],'-')==0)
					{
						$len++;
						$len++;
					}
					if(strcmp($charsT[$i],$charsMustBe[$i])==0)
					{
						continue;
					}
					if(strcmp($charsT[$i],'U')==0)
					{
						array_push($errs,'<b> الصورة'.self::_getStateName($stateNo).' ('.$mustBeName.') :</b>يجب تسكين الحرف '.self::_getCharName($len+1).' كي نحصل على تقطيع متوافق مع هذه الصورة');
						break;
					}
					if(strcmp($charsT[$i],'-')==0)
					{
						array_push($errs,'<b> الصورة'.self::_getStateName($stateNo).' ('.$mustBeName.') :</b>يجب أن يكون الحرف '.self::_getCharName($len).' متحركاً كي نحصل على تقطيع متوافق مع هذه الصورة');
						break;
					}
					if($i==count($charsMustBe)-1)
					{
						array_push($errs,'<b> الصورة'.self::_getStateName($stateNo).' ('.$mustBeName.') :</b>التقطيع الحالي لهذه التفعيلة أطول وزنيّاً من هذه الصورة');
						break;
					}
				}
			}
		}
		return $errs;
	}
	
	
	
	
	
	
	
	
	
	
	/* public functions begin */
	// تقطيع شطر عمودي 
	public function doFaraheedyShater($t,$isAjez){
		
		/* عملية تهيئة النص للتقطيع */
		$t=self::_specialCases($t); // clean text in embedded here !!
		$t=self::_moonySunnyLam($t);
		$t=self::_tanweenShaddeh($t,$isAjez);
		$t=self::_hamzetWasel($t);
		$oldText=$t; // هام من أجل أن يكون أساس حالات الإشباع
		/* نهاية تهيئة النص */
		
		
		
		/* عملية تقطيع النص و محاولة استخلاص البحر */
		$t=str_replace('#',' ',$t);
		while(strstr(' ',$t))
		{
			$t=str_replace(' ','',$t);
		}
		$arrodiWritten=$t;
		$chars=self::_getCharsOnly($arrodiWritten);
		$harakat=self::_getHarakatOnly($arrodiWritten);
		$rokaz=self::_rokzahKhoutayt($harakat);
		$ba7erName=self::_getBa7er($rokaz);
		/* نهاية عملية تقطيع النص و محاولة استخلاص البحر */
		
		//
		//	تحديد التفعيلات
		//
		if(strcmp($ba7erName,'unknown')!=0)
		{
			$tafa3eel=self::_getTafa3eel($rokaz,$chars,$ba7erName);
			for($i=0;$i<count($tafa3eel);$i++) // تضبيط عرض الألف المقصورة في النتائج كي لا تظهر متصلة !!
			{
				$tafa3eel[$i]=str_replace('ى','ى ',$tafa3eel[$i]);
				$tafa3eel[$i]=str_replace('ة','ة ',$tafa3eel[$i]);
			}
			$result=array("shater"=>$t,"arrodi"=>$arrodiWritten,"chars"=>$chars,"harakat"=>$harakat,"rokaz"=>$rokaz,'ba7erName'=>$ba7erName,'tafa3eel'=>$tafa3eel);
		}
		else
		{
			// الإشباااااااااااااع الجديد
			$result=self::_doEshbaa3Shater($oldText);
			if(!is_array($result))
			{
				if(strcmp($result,'unknownAlso')==0)
				{
					$result=array("shater"=>$t,"arrodi"=>$arrodiWritten,"chars"=>$chars,"harakat"=>$harakat,"rokaz"=>$rokaz,'ba7erName'=>'unknown','tafa3eel'=>array());
				}
			}
		}
		
		
		//var_dump($result);
		
		
		/* log begin */
		$data = array(
		   'request_type' => 'mezan',
		   'poem_type' => 'a',
		   'input' => $t,
		   'result' => json_encode($result)
		);
		$this->db->insert('requests',$data); 
		/* log end */
		
		return $result;
	}
	
	// تقطيع قصيدة تفعيلة
	public function doFaraheedyTafeela($t){
		/* عملية تهيئة النص للتقطيع */
		$t='#'.$t.'#';
		$t=preg_replace(array('/\s/','/\n/','/\r/'),array('#','#','#'),$t);
		
		$t=self::_specialCases($t); // clean text in embedded here !!
		$t=self::_moonySunnyLam($t);
		$t=self::_tanweenShaddeh($t,false);
		$t=self::_hamzetWasel($t);
		$oldText=$t; // هام من أجل أن يكون أساس حالات الإشباع
		/* نهاية تهيئة النص */
		
		
		/* عملية تقطيع النص و محاولة استخلاص البحر */
		$t=str_replace('#',' ',$t);
		while(strstr(' ',$t))
		{
			$t=str_replace(' ','',$t);
		}
		$arrodiWritten=$t;
		$chars=self::_getCharsOnly($arrodiWritten);
		$harakat=self::_getHarakatOnly($arrodiWritten);
		$rokaz=self::_rokzahKhoutayt($harakat);
		
		/* نهاية عملية تقطيع النص و محاولة استخلاص البحر */
		$ba7erName=self::_whatTafeelaPoemOn($rokaz);
		
		if(strcmp($ba7erName,'unknown')==0)
		{
			/* log begin */
			$data = array(
			   'request_type' => 'mezan',
			   'poem_type' => 't',
			   'input' => $t,
			   'result' => json_encode(array('poemErr'=>'لم يتم التعرّف على وزن هذه القصيدة للأسف , تأكّد من إدخال نصّ القصيدة بشكل صحيح'))
			);
			$this->db->insert('requests',$data); 
			/* log end */
			return array('poemErr'=>'لم يتم التعرّف على وزن هذه القصيدة للأسف , تأكّد من إدخال نصّ القصيدة بشكل صحيح');
		}
		else
		{
			/* log begin */
			$data = array(
			   'request_type' => 'mezan',
			   'poem_type' => 't',
			   'input' => $t,
			   'result' => json_encode(self::_getTafaeelForTafeelaPoem($ba7erName,$rokaz,$chars))
			);
			$this->db->insert('requests',$data); 
			/* log end */
			return self::_getTafaeelForTafeelaPoem($ba7erName,$rokaz,$chars);
		}
		
	
		
		//var_dump($tafeelaName);
		//return $t;
	}
	
	// دالة تحليل القوافي
	public function qawafeeAnalysis($baits){
		// تحديد البيت الأوّل (أوّل عجز في المصفوفة)
		$results=array();
		$beginingIndex=-1;
		for($i=0;$i<count($baits);$i++)
		{
			if(strcmp($baits[$i],'empty')!=0)
			{
				$beginingIndex=$i;
				break;
			}
			array_push($results,'empty');
		}
		
		if($beginingIndex==-1)
		{
			return 'emptyAll';
		}
		
		
		// تحليل قافية البيت الأوّل لاعتبارها القافية المرجعيّة
		$baseQafeeh=self::_analyseQafeah($baits[$beginingIndex]);
		array_push($results,$baseQafeeh);
		
		for($i=$beginingIndex+1;$i<count($baits);$i++)
		{
			if(strcmp($baits[$i],'empty')!=0)
			{
				$currentQafeeh=self::_analyseQafeah($baits[$i]);
				$qErrs=array();
				//مقارنة قافية البيت الحالي بقافية البيت الأول لتحديد عيوب القافية (الاختلافات)
				// أولاً الروي
				if(strcmp($currentQafeeh['rawee'],$baseQafeeh['rawee'])!=0)
				{
					array_push($qErrs,'قافية هذا البيت مختلفة كليَّاً عن قافية القصيدة و ذلك <b>لاختلاف الرَّويِّ</b> بين القافيتين.');
				}
				else if(strcmp($currentQafeeh['wasel'],$baseQafeeh['wasel'])!=0)
				{
					if((strcmp($currentQafeeh['wasel'],'اْ')==0 && strcmp($baseQafeeh['wasel'],'ىْ')==0) ||
					   (strcmp($currentQafeeh['wasel'],'ىْ')==0 && strcmp($baseQafeeh['wasel'],'اْ')==0))
					{
					
					}
					else
					{
						array_push($qErrs,'قافية هذا البيت مختلفة عن قافية القصيدة بسبب <b>اختلاف حرف الوصل</b>.');
					}
				}
				else
				{
					
					// ثانياً التأسيس
					if(strcmp($currentQafeeh['ta2ses'],'')!=0 && strcmp($baseQafeeh['ta2ses'],'')==0)
					{
						array_push($qErrs,'لقد قمت باستعمال ألف التأسيس في قافية هذا البيت في حين أنَّ قافية القصيدة ليست مؤسَّسة و هذا عيب من عيوب القافية يعرف بـ<b>سناد التأسيس</b>.');
					}
					else if(strcmp($currentQafeeh['ta2ses'],'')==0 && strcmp($baseQafeeh['ta2ses'],'')!=0)
					{
						array_push($qErrs,'يجب أن تُؤَسَّسَ قافية هذا البيت بألف التأسيس !');
					}
					// ثالثاً الردف
					if(strcmp($currentQafeeh['redf'],'')!=0 && strcmp($baseQafeeh['redf'],'')==0)
					{
						array_push($qErrs,'لقد قمت باستعمال ردف للقافية في قافية هذا البيت في حين أنَّ قافية القصيدة ليست مردفة و هذا عيب من عيوب القافية يعرف بـ<b>سناد الرِّدف</b>.');
					}
					else if(strcmp($currentQafeeh['redf'],'')==0 && strcmp($baseQafeeh['redf'],'')!=0)
					{
						array_push($qErrs,'يجب أن تُرْدِفَ قافية هذا البيت بحرف الرِّدف المناسب قبل الرَّوي مباشرةً !');
					}
					else if(strcmp($currentQafeeh['redf'],'')!=0 && strcmp($baseQafeeh['redf'],'')!=0)
					{
						if((strcmp($currentQafeeh['redf'],'يْ')==0 || strcmp($currentQafeeh['redf'],'وْ')==0) && (strcmp($baseQafeeh['redf'],'ا')==0 || strcmp($baseQafeeh['redf'],'اْ')==0))
						{
							array_push($qErrs,'لا يمكن أن تجتمع الياء أو الواو كردف مع الألف كردف !');
						}
						else if((strcmp($currentQafeeh['redf'],'اْ')==0 || strcmp($currentQafeeh['redf'],'ا')==0) && (strcmp($baseQafeeh['redf'],'وْ')==0 || strcmp($baseQafeeh['redf'],'يْ')==0))
						{
							array_push($qErrs,'لا يمكن أن تجتمع الياء أو الواو كردف مع الألف كردف !');
						}
					}
					// رابعاً الخروج
					if(strcmp($currentQafeeh['rawee'],$baseQafeeh['rawee'])!=0)
					{
						array_push($qErrs,'الخروج مختلف في البيت الحالي عن الخروج في قافية القصيدة');
					}
				}
				$currentQafeeh['errors']=$qErrs;
				array_push($results,$currentQafeeh);
			}
			else
			{
				array_push($results,'empty');
			}
		}
		/* log begin */
		$data = array(
		   'request_type' => 'aQafeah',
		   'poem_type' => 'a',
		   'input' => json_encode($baits),
		   'result' => json_encode($results)
		);
		$this->db->insert('requests',$data); 
		/* log end */
		return $results;
	}
	
	// دالة معالج الكتابة العمودية
	public function doAmoodyWizard($t,$isAjez,$ruleArr,$ruleNamesArr){
		
		/* عملية تهيئة النص للتقطيع */
		$t=self::_specialCases($t); // clean text in embedded here !!
		$t=self::_moonySunnyLam($t);
		$t=self::_tanweenShaddeh($t,$isAjez);
		$t=self::_hamzetWasel($t);
		$oldText=$t; // هام من أجل أن يكون أساس حالات الإشباع
		/* نهاية تهيئة النص */
		
		
		
		/* عملية تقطيع النص و محاولة استخلاص البحر */
		$t=str_replace('#',' ',$t);
		while(strstr(' ',$t))
		{
			$t=str_replace(' ','',$t);
		}
		$arrodiWritten=$t;
		
		$chars=self::_getCharsOnly($arrodiWritten);
		$harakat=self::_getHarakatOnly($arrodiWritten);
		$rokaz=self::_rokzahKhoutayt($harakat);
		
		$res=array();
		for($indexX=0;$indexX<count($ruleArr);$indexX++)
		{
			$tafeela=$ruleArr[$indexX];
			$tafeelaNames=$ruleNamesArr[$indexX];
			$isOk=false;
			for($i=0;$i<count($tafeela);$i++)
			{
				$currentStatus=substr($rokaz,0,strlen($tafeela[$i]));
				$currentStatusName='';
				for($indexOfName=0;$indexOfName<count($tafeela);$indexOfName++)
				{
					if(strcmp($tafeela[$indexOfName],$currentStatus)==0)
					{
						$currentStatusName=$tafeelaNames[$indexOfName];
					}
				}
				
				if(strcmp($tafeela[$i],$currentStatus)==0)
				{
					$currentStatusArr=str_split($currentStatus);
					for($xi=0,$len=0;$xi<count($currentStatusArr);$xi++)
					{
						if(strcmp($currentStatusArr[$xi],'U')==0)
						{
							$len++;
						}
						else if(strcmp($currentStatusArr[$xi],'-')==0)
						{
							$len++;
							$len++;
						}
					}
					$currentChars=substr($chars,0,$len*2);
					$chars=substr($chars,strlen($currentChars));
					
					
					$currentChars=str_replace('ى','ى ',$currentChars);
					$currentChars=str_replace('ة','ة ',$currentChars);
					
					/*if(strcmp($currentStatusName,'')==0)
					{
						$currentChars='';
					}*/
					
					
					$rokaz=substr($rokaz,strlen($tafeela[$i]));
					$res[count($res)]=array('status'=>'ok','taf3eela'=>$currentStatusName,'chars'=>$currentChars);
					$isOk=true;
					break;
				}
			}
			if(!$isOk)
			{
				
				$currentStatus=substr($rokaz,0,strlen($tafeela[0]));
			
				$currentStatusName='';
				for($indexOfName=0;$indexOfName<count($tafeela);$indexOfName++)
				{
					if(strcmp($tafeela[$indexOfName],$currentStatus)==0)
					{
						$currentStatusName=$tafeelaNames[$indexOfName];
					}
				}
			
				$currentStatusArr=str_split($currentStatus);
				for($xi=0,$len=0;$xi<count($currentStatusArr);$xi++)
				{
					if(strcmp($currentStatusArr[$xi],'U')==0)
					{
						$len++;
					}
					else if(strcmp($currentStatusArr[$xi],'-')==0)
					{
						$len++;
						$len++;
					}
				}
				
				$currentChars=substr($chars,0);
				$chars=substr($chars,strlen($currentChars));
				
				
				$currentChars=str_replace('ى','ى ',$currentChars);
				$currentChars=str_replace('ة','ة ',$currentChars);
			
				/*if(strcmp($currentStatusName,'')==0)
				{
					$currentChars='';
				}*/
				
				$rokaz=substr($rokaz,strlen($tafeela[0]));
				$mustBe=$tafeela;
				$res[count($res)]=array('status'=>'err','taf3eela'=>$currentStatusName,'chars'=>$currentChars,'errs'=>self::_compareWithTafeela($currentStatus,$mustBe,$tafeelaNames));	
				
				break; // عند حصول خطأ في تفعيلة ما وزنياً لا يتم المتابعة لأن التفعيلات التالية خاطئة بالتأكيد بسبب اعتمادها على هذه !!!
				// من الممكن عمل خيار إضافي (عرض كل أخطاءالتفعيلات) و عند تفعيله مثلاً نلغي هذه البريك !
			}
		}
		
		/* log begin */
		$data = array(
		   'request_type' => 'wizard',
		   'poem_type' => 'a',
		   'input' => $t,
		   'result' => json_encode($res)
		);
		$this->db->insert('requests',$data); 
		/* log end */
		return $res;
		
	}
	
	// دالة معالج الكتابة للقصيدة التفعيلية
	public function doTafeelaWizard($t,$ruleArr,$ruleNamesArr){
		
		/* عملية تهيئة النص للتقطيع */
		$t=self::_specialCases($t); // clean text in embedded here !!
		$t=self::_moonySunnyLam($t);
		$t=self::_tanweenShaddeh($t,false);
		$t=self::_hamzetWasel($t);
		$oldText=$t; // هام من أجل أن يكون أساس حالات الإشباع
		/* نهاية تهيئة النص */
		
		
		
		/* عملية تقطيع النص و محاولة استخلاص البحر */
		$t=str_replace('#',' ',$t);
		while(strstr(' ',$t))
		{
			$t=str_replace(' ','',$t);
		}
		$arrodiWritten=$t;
		
		$chars=self::_getCharsOnly($arrodiWritten); // الحروف فقط
		$harakat=self::_getHarakatOnly($arrodiWritten); // الحركات فقط
		$rokaz=self::_rokzahKhoutayt($harakat); // الركز و الخطيطات فقط
		
		$res=array();
		$tafeela=$ruleArr[0];
		$tafeelaNames=$ruleNamesArr[0];
		do
		{
			
			$isOk=false;
			
			//var_dump($tafeela);
			for($i=0;$i<count($tafeela);$i++)
			{
				$currentStatus=substr($rokaz,0,strlen($tafeela[$i]));
				$currentStatusName='';
				for($indexOfName=0;$indexOfName<count($tafeela);$indexOfName++)
				{
					if(strcmp($tafeela[$indexOfName],$currentStatus)==0)
					{
						$currentStatusName=$tafeelaNames[$indexOfName];
					}
				}
				
				if(strcmp($tafeela[$i],$currentStatus)==0)
				{
					$currentStatusArr=str_split($currentStatus);
					for($xi=0,$len=0;$xi<count($currentStatusArr);$xi++)
					{
						if(strcmp($currentStatusArr[$xi],'U')==0)
						{
							$len++;
						}
						else if(strcmp($currentStatusArr[$xi],'-')==0)
						{
							$len++;
							$len++;
						}
					}
					$currentChars=substr($chars,0,$len*2);
					$chars=substr($chars,strlen($currentChars));
					
					
					$currentChars=str_replace('ى','ى ',$currentChars);
					$currentChars=str_replace('ة','ة ',$currentChars);
					
					/*if(strcmp($currentStatusName,'')==0)
					{
						$currentChars='';
					}*/
					
					
					$rokaz=substr($rokaz,strlen($tafeela[$i]));
					$res[count($res)]=array('status'=>'ok','taf3eela'=>$currentStatusName,'chars'=>$currentChars);
					$isOk=true;
				//	$rokaz='';
					break;
					
				}
			}
			if(!$isOk)
			{
				
				$currentStatus=substr($rokaz,0,strlen($tafeela[0]));
			
				$currentStatusName='';
				for($indexOfName=0;$indexOfName<count($tafeela);$indexOfName++)
				{
					if(strcmp($tafeela[$indexOfName],$currentStatus)==0)
					{
						$currentStatusName=$tafeelaNames[$indexOfName];
					}
				}
			
				$currentStatusArr=str_split($currentStatus);
				for($xi=0,$len=0;$xi<count($currentStatusArr);$xi++)
				{
					if(strcmp($currentStatusArr[$xi],'U')==0)
					{
						$len++;
					}
					else if(strcmp($currentStatusArr[$xi],'-')==0)
					{
						$len++;
						$len++;
					}
				}
				
				$currentChars=substr($chars,0,$len*2);
				$chars=substr($chars,strlen($currentChars));
				
				
				$currentChars=str_replace('ى','ى ',$currentChars);
				$currentChars=str_replace('ة','ة ',$currentChars);
			
				/*if(strcmp($currentStatusName,'')==0)
				{
					$currentChars='';
				}*/
				
				$rokaz=substr($rokaz,strlen($tafeela[0]));
				$mustBe=$tafeela;
				$res[count($res)]=array('status'=>'err','taf3eela'=>$currentStatusName,'chars'=>$currentChars,'errs'=>self::_compareWithTafeela($currentStatus,$mustBe,$tafeelaNames));	
				//$rokaz='';
				//break; // في شعر التفعيلة من الضروري تعطيل هذه البريك لأنه قد يكون ما يلي صحيح و الخطأ في تفعيلة واحدة
			}
		}while(strlen($rokaz)>0);
		
		/* log begin */
		$data = array(
		   'request_type' => 'wizard',
		   'poem_type' => 't',
		   'input' => $t,
		   'result' => json_encode($res)
		);
		$this->db->insert('requests',$data); 
		/* log end */
		return $res;
		
	}
	
	/* public functions end */
}