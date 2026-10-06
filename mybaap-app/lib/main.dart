import 'package:flutter/material.dart';
import 'package:mybaap/core/theme/app_theme.dart';
import 'package:mybaap/modules/account/views/account_view.dart';
import 'package:mybaap/modules/auth/providers/auth_provider.dart';
import 'package:mybaap/modules/auth/views/mobile_view.dart';
import 'package:mybaap/modules/auth/views/otp_view.dart';
import 'package:mybaap/modules/auth/views/splash_view.dart';
import 'package:mybaap/modules/help/providers/content_provider.dart';
import 'package:mybaap/modules/help/views/cms_page_view.dart';
import 'package:mybaap/modules/help/views/faq_view.dart';
import 'package:mybaap/modules/home/models/catalog_models.dart';
import 'package:mybaap/modules/home/providers/home_provider.dart';
import 'package:mybaap/modules/home/views/home_view.dart';
import 'package:mybaap/modules/player/views/player_view.dart';
import 'package:mybaap/modules/subscription/providers/subscription_provider.dart';
import 'package:mybaap/modules/subscription/views/subscription_view.dart';
import 'package:mybaap/routes/app_routes.dart';
import 'package:mybaap/services/api_client.dart';
import 'package:mybaap/services/token_store.dart';
import 'package:provider/provider.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  final tokenStore = TokenStore();
  final api = ApiClient(tokenStore);
  runApp(MyBaapApp(api: api, tokenStore: tokenStore));
}

class MyBaapApp extends StatelessWidget {
  const MyBaapApp({super.key, required this.api, required this.tokenStore});

  final ApiClient api;
  final TokenStore tokenStore;

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider(api, tokenStore)),
        ChangeNotifierProvider(create: (_) => HomeProvider(api)),
        ChangeNotifierProvider(create: (_) => SubscriptionProvider(api)),
        ChangeNotifierProvider(create: (_) => ContentProvider(api)),
      ],
      child: MaterialApp(
        title: 'MyBaap',
        debugShowCheckedModeBanner: false,
        theme: AppTheme.light(),
        navigatorKey: AppRoutes.navigatorKey,
        initialRoute: AppRoutes.splash,
        onGenerateRoute: (settings) {
          switch (settings.name) {
            case AppRoutes.mobile:
              return MaterialPageRoute(builder: (_) => const MobileView());
            case AppRoutes.otp:
              final mobile = settings.arguments as String? ?? '';
              return MaterialPageRoute(builder: (_) => OtpView(mobile: mobile));
            case AppRoutes.home:
              return MaterialPageRoute(builder: (_) => const HomeView());
            case AppRoutes.player:
              final video = settings.arguments as VideoItem;
              return MaterialPageRoute(builder: (_) => PlayerView(video: video));
            case AppRoutes.subscription:
              return MaterialPageRoute(builder: (_) => const SubscriptionView());
            case AppRoutes.account:
              return MaterialPageRoute(builder: (_) => const AccountView());
            case AppRoutes.page:
              final slug = settings.arguments as String? ?? '';
              return MaterialPageRoute(builder: (_) => CmsPageView(slug: slug));
            case AppRoutes.faq:
              return MaterialPageRoute(builder: (_) => const FaqView());
            case AppRoutes.splash:
            default:
              return MaterialPageRoute(builder: (_) => const SplashView());
          }
        },
      ),
    );
  }
}
