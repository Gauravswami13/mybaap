import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/auth/providers/auth_provider.dart';
import 'package:mybaap/routes/app_routes.dart';
import 'package:provider/provider.dart';

class SplashView extends StatefulWidget {
  const SplashView({super.key});

  @override
  State<SplashView> createState() => _SplashViewState();
}

class _SplashViewState extends State<SplashView> {
  @override
  void initState() {
    super.initState();
    _boot();
  }

  Future<void> _boot() async {
    final auth = context.read<AuthProvider>();
    final ok = await auth.hasSession();
    if (!mounted) return;
    Navigator.pushReplacementNamed(context, ok ? AppRoutes.home : AppRoutes.mobile);
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      backgroundColor: ColorConstant.bgCl,
      body: Center(
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            CircleAvatar(
              radius: 18,
              backgroundColor: ColorConstant.primaryCl,
              child: Icon(Icons.play_arrow_rounded, color: ColorConstant.white),
            ),
            SizedBox(width: 10),
            Text(
              'MYBAAP',
              style: TextStyle(color: ColorConstant.primaryCl, fontFamily: FontsStyle.bold, fontSize: 32, letterSpacing: 1),
            ),
          ],
        ),
      ),
    );
  }
}
