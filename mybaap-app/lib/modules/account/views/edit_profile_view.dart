import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/app_image.dart';
import 'package:mybaap/core/common_widgets/custom_button.dart';
import 'package:mybaap/core/common_widgets/custom_input_fields.dart';
import 'package:mybaap/core/common_widgets/green_app_bar.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/auth/providers/auth_provider.dart';
import 'package:mybaap/services/api_client.dart';
import 'package:provider/provider.dart';

class EditProfileView extends StatefulWidget {
  const EditProfileView({super.key});

  @override
  State<EditProfileView> createState() => _EditProfileViewState();
}

class _EditProfileViewState extends State<EditProfileView> {
  final _name = TextEditingController();
  final _mobile = TextEditingController();

  @override
  void initState() {
    super.initState();
    final auth = context.read<AuthProvider>();
    _name.text = auth.user?.name ?? '';
    _mobile.text = auth.user?.mobile ?? '';
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      await auth.loadProfile();
      if (!mounted) return;
      _name.text = auth.user?.name ?? '';
      _mobile.text = auth.user?.mobile ?? '';
    });
  }

  @override
  void dispose() {
    _name.dispose();
    _mobile.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    final name = _name.text.trim();
    if (name.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Name is required')));
      return;
    }
    try {
      await context.read<AuthProvider>().saveProfile(name);
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Profile updated')));
      Navigator.pop(context);
    } on ApiException catch (error) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(error.message)));
    }
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final name = auth.user?.displayName ?? 'M';
    return Scaffold(
      backgroundColor: ColorConstant.bgCl,
      appBar: const GreenAppBar(title: 'Edit Profile'),
      body: Column(
        children: [
          Expanded(
            child: ListView(
              padding: const EdgeInsets.fromLTRB(16, 16, 16, 24),
              children: [
                Center(
                  child: InkWell(
                    onTap: () => context.read<AuthProvider>().pickPhoto(),
                    child: Stack(
                      children: [
                        ProfileAvatar(
                          name: name,
                          photoUrl: auth.user?.photoUrl,
                          filePath: auth.pendingPhotoPath,
                          radius: 44,
                        ),
                        Positioned(
                          right: 0,
                          bottom: 0,
                          child: Container(
                            padding: const EdgeInsets.all(6),
                            decoration: BoxDecoration(
                              color: ColorConstant.primaryCl,
                              shape: BoxShape.circle,
                              border: Border.all(color: ColorConstant.lightBorderCl),
                            ),
                            child: const Icon(Icons.camera_alt_outlined, size: 14, color: ColorConstant.white),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 8),
                const Center(
                  child: Text(
                    'Change photo',
                    style: TextStyle(color: ColorConstant.lightGrayIcCl, fontSize: 12, fontFamily: FontsStyle.regular),
                  ),
                ),
                const SizedBox(height: 20),
                CustomTextField(
                  labelRequired: true,
                  labelText: 'Name',
                  hintText: 'Name',
                  controller: _name,
                  maxLength: 50,
                  textCapitalization: TextCapitalization.words,
                  prefix: const Icon(Icons.person_outline, size: 22, color: ColorConstant.lightGrayIcCl),
                ),
                const SizedBox(height: 16),
                CustomTextField(
                  labelText: 'Mobile number',
                  labelRequired: true,
                  hintText: 'Mobile number',
                  readOnly: true,
                  controller: _mobile,
                  prefix: const Text(
                    '+91',
                    style: TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w600,
                      color: ColorConstant.darkGrayCl,
                      fontFamily: FontsStyle.semiBold,
                    ),
                  ),
                ),
              ],
            ),
          ),
          SafeArea(
            top: false,
            child: Padding(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 12),
              child: CustomButton(text: auth.loading ? 'Saving...' : 'Save', enabled: !auth.loading, onPressed: _save),
            ),
          ),
        ],
      ),
    );
  }
}
