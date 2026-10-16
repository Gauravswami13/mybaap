import 'dart:io';

import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';

class AppImage extends StatelessWidget {
  const AppImage({
    super.key,
    this.url,
    this.icon = Icons.image_outlined,
    this.fit = BoxFit.cover,
  });

  final String? url;
  final IconData icon;
  final BoxFit fit;

  @override
  Widget build(BuildContext context) {
    final path = url?.trim() ?? '';
    if (path.isEmpty) return _placeholder(icon);
    return Image.network(
      path,
      fit: fit,
      width: double.infinity,
      height: double.infinity,
      errorBuilder: (_, _, _) => _placeholder(icon),
    );
  }

  static Widget _placeholder(IconData icon) {
    return ColoredBox(
      color: ColorConstant.primaryMintCl,
      child: Center(child: Icon(icon, color: ColorConstant.primaryDarkCl, size: 28)),
    );
  }
}

class ProfileAvatar extends StatelessWidget {
  const ProfileAvatar({
    super.key,
    required this.name,
    this.photoUrl,
    this.filePath,
    this.radius = 25,
  });

  final String name;
  final String? photoUrl;
  final String? filePath;
  final double radius;

  @override
  Widget build(BuildContext context) {
    ImageProvider? image;
    if (filePath != null && filePath!.isNotEmpty) {
      image = FileImage(File(filePath!));
    }
    final url = photoUrl?.trim() ?? '';
    return CircleAvatar(
      radius: radius,
      backgroundColor: ColorConstant.primaryCl,
      backgroundImage: image,
      child: image != null
          ? null
          : url.isEmpty
              ? Text(
                  name.isEmpty ? 'M' : name[0].toUpperCase(),
                  style: TextStyle(color: ColorConstant.white, fontFamily: 'Lexend-SemiBold', fontSize: radius * 0.7),
                )
              : ClipOval(
                  child: SizedBox(
                    width: radius * 2,
                    height: radius * 2,
                    child: AppImage(url: url, icon: Icons.person_outline),
                  ),
                ),
    );
  }
}
