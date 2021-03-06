<!doctype html>
<html class="no-js">

	<head>
		
		<title>الفراهيدي - مشروع حوسبة عروض الشعر العربي و قافيته على الويب</title>
		
		
		<?php include( dirname(dirname(__FILE__)) .'/views/inc/head.php'); ?>	
		


		<!-- mobile-nav -->
		<div id="mobile-nav-holder">
			<div class="wrapper">
				<ul id="mobile-nav">
					
					<li><a href="<?php echo base_url('mezan'); ?>">ميزان القصيدة</a></li>
					<li><a href="<?php echo base_url('wizard'); ?>">معالج كتابة قصيدة موزونة</a></li>
					<li><a href="<?php echo base_url('lessons'); ?>">العَروض نظريَّاً</a></li>
					<li class="current-menu-item"><a href="<?php echo base_url('info'); ?>">عن المشروع</a></li>
				</ul>
				<div id="nav-open"><a href="#">ابدأ</a></div>
			</div>
		</div>
		<!-- ENDS mobile-nav -->
			
		<header>
			
				
			<div class="wrapper">
					
				<a href="<?php echo base_url(''); ?>" id="logo"><img  src="<?php echo base_url('assets'); ?>/img/logo.png" alt="الفراهيدي - مشروع حوسبة عروض الشعر العربي على الويب"></a>
				
				<nav>
					<ul id="nav" class="sf-menu">
						
						<li><a href="<?php echo base_url('mezan'); ?>">ميزان القصيدة<span class="subheader">التقطيع العروضي الآلي</span></a>
						<li><a href="<?php echo base_url('wizard'); ?>">معالج كتابة قصيدة<span class="subheader">اكتب قصيدتك الموزونة</span></a></li>
						<li><a href="<?php echo base_url('lessons'); ?>">العَروض نظريَّاً<span class="subheader">ضوابط البحور و موسيقاها</span></a></li>
						<li class="current-menu-item"><a href="<?php echo base_url('info'); ?>">عن المشروع<span class="subheader">معلومات و سياسات</span></a></li>
						
					</ul>
				</nav>
				
				<div class="clearfix"></div>
				
			</div>
		</header>
	
	
	
	
		<!-- MAIN -->
		<div id="main">
				
			<!-- social -->
			<?php include('inc/social.php'); ?>
			<!-- ENDS social -->
			
			
			
			<!-- Content -->
			<div id="content">
		
	        	
	        	<br/>
	        	<!-- masthead -->
		        <div id="masthead">
					<span class="head"><img style="margin:0 20px 0 0;width:60px;height:60px;" src="<?php echo base_url("assets/img"); ?>/twitter.png" /></span>
					<div id="lastNews">
					  <ul>
						<li>يجري تحميل الأخبار حاليَّاً ...</li>
					  </ul>
					</div>
				</div>
				<script type="text/javascript">
					$(document).ready(function(){

						$('#lastNews').twittie({
							dateFormat: '%b. %d, %Y',
							template: '<li>{{tweet}} <span class="datetwitter">{{date}}</span></li> ',
							count: 5
						});
		
						
					});
				</script>
	        	<!-- ENDS masthead -->
	        	<br/><br/>
	        	
	        	
	        	<!-- featured -->
	        	<h1 class="home-block-heading">عن مشروع الفراهيدي</h1>
				
				<div class="pageContentFixed">
					<p>أهلاً و سهلاً بكم في نسخة الويب لـ <b>مشروع الفراهيدي</b> (مشروع حوسبة عروض و قافية الشعر العربيّ).</p><br/>
					<h2>ما هو الفراهيدي ؟</h2>
					<p><b>الفراهيدي</b> نظامٌ حاسوبيٌّ يهدفُ إلى توظيفِ برمجيَّاتِ الحاسبِ الآليِّ في معالجةِ الشِّعرِ العربيِّ عَروضيَّاً (إيقاعيَّاً) بحيث يصبحُ في مقدورِ الآلةِ تشريحُ أيِّ نصٍّ شِعريٍّ (أو غيرِ شِعريٍّ) تشريحاً صحيحاً منَ النَّاحيةِ العروضيَّةِ (الإيقاعيَّةِ) , و قد استُوْحِيَ اسمُ المشروعِ من اسمِ العالِمِ الجليلِ (الخليلُ بنُ أحمدَ الفراهيديّ) الذي اكتشفَ أوزانَ (إيقاعاتِ) الشِّعرِ العربيِّ و قامَ بصياغةِ قواعدِهَا و وضعِ مصطلَحاتِ عِلْمِ العَروضِ من خلالِ استقرائه للمُنجَزِ الشِّعريِّ العربيِّ حتَّى زمانِهِ.</p>
					<br/>
					<h2>مَنْ سيستفيد من الفراهيدي و كيف ؟</h2>
					<p>يستهدف <b>الفراهيدي</b> جمهورَ الشُّعراءِ الشَّبابِ و طلّابِ الأدبِ العربيِّ و محبِّيهِ منَ الرَّاغبينَ في إتقانِ عَروضِ الشِّعرِ العربيِّ و امتلاكِ ناصيةِ الكتابةِ الوزنيَّةِ للمُساهمةِ في رَفْدِ حركةِ الإبداعِ الشِّعريِّ العربيِّةِ شِعراً و نقداً و ذلكَ -كما أتصوَّرُ- منْ خلالِ وجودِهِ أداةً تتلاءَمُ و متطلَّباتِ العصرِ بينَ أيدي هؤلاءِ المبدعينَ.</p>
					<br/>
					<h2>و ماذا عن هذا الموقع ؟</h2>
					<p>يقدِّم هذا الموقع خدماته و معلوماته المتعلّقة بعلم العروض كمايلي :<br/>
						<ol>
							<li><b>المعلومات النظريَّة :</b> تَقَدِّمُ بوَّابةُ "العروض نظريَّاً" مرجعاً نظريَّاً بشرحٍ مبسَّطٍ لبحورِ الشِّعر كلَّها بحراً بحراً مع جوازاتِها و الزُّحافاتِ و العِلَلِ التي تطرأُ عليها كما تظهِرُ أيضاً الموسيقى الخاصَّةَ بكلِّ بحرٍ مِنْ خلالِ تأديةِ ضابطِ البحرِ على مقاماتٍ غنائيَّةٍ بأمثلةٍ من عيون الشِّعر العربيّ.</li>
							<br/>
							<li><b>المُعالِجات :</b> تشكِّل "المُعالِجاتْ" Wizards القِسْمَ البرمجيَّ الذي يمثِّلُ نصيبَ الأسدِ منَ المشروعِ و هيَ على نوعين :<br/>
								
								<ul>
									<li><b>ميزانُ القصيدةِ :</b> يساعدُ ميزانُ القصيدةِ الدَّارسَ على القيامِ بتشريحِ أيِّ نصٍّ شعريٍّ تشريحاً عروضيَّاً و على تحديدِ تفعيلاتِ كُلِّ بيتٍ على حِدَةٍ بشكلٍ تفصيليٍّ , كما يساعد الدَّارس في تحليل قوافي القصيدة و معرفة كل الأخطاء الشائعة التي قد يرتكبها الشعراء الشباب في قوافي بعض الأبيات مثل أخطاء سناد الردف و سناد التأسيس ... إلخ.</li>
									<li><b>مُعالِجُ كتابةِ قصيدةٍ :</b> يساعدُ معالِجُ كتابةِ القصيدةِ الشَّاعرَ الشَّابَّ على كتابةِ قصيدَتِهِ الشِّعريَّةِ الموزونةِ (بيتاً فبيتاً إن كانتْ عموديَّةً أو مقطعاً مقطعاً إنْ كانتْ تفعيليَّةً) و ذلك باستهدافِ الشَّاعرِ الشَّابِّ بحراً شعريَّاً معيَّناً منَ البدايةِ مما يتيحُ للبرمجيَّةِ مساعدَتَهُ في معرفةِ أماكنِ النجاحِ و أماكنِ الإخفاقِ و تقديمِ النصائحِ بشأنِ الأخيرةِ بسهولةٍ و يسرٍ كَبيرينِ , و قد اجتهدنا (و ما نزالُ) لجعلِ هذه المعالِجات أبسطَ استخداماً و أكثر فاعليَّة و جدوى.</li>
								</ul>
							</li>
						</ol>
					</p>
					<br/>
					<h2>ما هي آفاق تطوير هذا المشروع ؟</h2>
					<p>يعتبرُ إطلاقُ مشروع <b>الفراهيدي</b> على الويب بصورتِهِ الحاليَّةِ المرحلةَ الثَّانيةَ بعدَ مرحلةٍ أولى جرتْ في البداية كتجربةٍ برمجيَّةٍ خاصَّةٍ بي فقط و استمرَّت هكذا لسنواتٍ طِوال , و إذا جرتْ الأمورُ كما أتمنَّى و أتوقَّعُ فإنَّ لهذا المشروعِ (في مخيِّلتي الآنَ) آفاقَ تطويرٍ كبيرةٍ أتمنَّى أن يكونَ تفاعُلُكمْ معهُ سبباً في إطلاقها لتبصرَ النَّورَ في مرحلةٍ قادمة بإذن الله.</p>
					<br/>
					
					<h2>من أين أبدأ الآن ؟</h2>
					<p>قبل أن تبدأ باستخدام المعالجات ننصحك أن تأخذ وقتك في قراءة <a style="color:rgb(187, 0, 0) !important;" href="<?php echo base_url('info/help'); ?>"><b>دليل الاستخدام</b></a> بشيء من التركيز لتستفيد من كامل إمكانيّات البرمجيّة.</p>
					<br/>
					<!--<p class="leftA">-- مصمم و منفّذ المشروع : المهندس مختار سيّد صالح</p>-->
					
					<p style="text-align:center;color:rgb(187, 0, 0) !important;"> شكراً جزيلاً لكم <br/><small>آخر تحديث : كوالالمبور 25-5-2016</small></p>
					
					
					<div class="clearfix"></div>	
					
	        	</div>
	        	
	      
				
				
			
			</div>
			<!-- ENDS content -->
			
			
			<?php include('inc/foot.php'); ?>