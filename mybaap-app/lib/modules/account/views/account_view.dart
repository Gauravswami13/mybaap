import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/auth/providers/auth_provider.dart';
import 'package:mybaap/routes/app_routes.dart';
import 'package:provider/provider.dart';

class AccountView extends StatelessWidget {
  const AccountView({super.key});

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    return Scaffold(
      appBar: AppBar(title: const Text('Profile', style: TextStyle(fontFamily: FontsStyle.semiBold))),
      body: ListView(
        children: [
          ListTile(
            title: Text(auth.user?.displayName ?? 'MyBaap', style: const TextStyle(fontFamily: FontsStyle.semiBold)),
            subtitle: Text(auth.user?.mobile ?? ''),
          ),
          const Divider(),
          _tile(context, 'Privacy Policy', () => Navigator.pushNamed(context, AppRoutes.page, arguments: 'privacy-policy')),
          _tile(context, 'Terms', () => Navigator.pushNamed(context, AppRoutes.page, arguments: 'terms')),
          _tile(context, 'FAQ', () => Navigator.pushNamed(context, AppRoutes.faq)),
        ],
      ),
    );
  }

  Widget _tile(BuildContext context, String title, VoidCallback onTap) {
    return ListTile(
      title: Text(title, style: const TextStyle(fontFamily: FontsStyle.medium)),
      trailing: const Icon(Icons.chevron_right, color: ColorConstant.primaryCl),
      onTap: onTap,
    );
  }
}
