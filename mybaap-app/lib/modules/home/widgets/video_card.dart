import 'package:flutter/material.dart';
import 'package:mybaap/core/common_widgets/app_image.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/home/models/catalog_models.dart';

class VideoCard extends StatelessWidget {
  const VideoCard({super.key, required this.video, required this.onTap});

  final VideoItem video;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.symmetric(vertical: 6),
      color: ColorConstant.surfaceCl,
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
        side: const BorderSide(color: ColorConstant.lightBorderCl),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(10),
          child: Row(
            children: [
              ClipRRect(
                borderRadius: BorderRadius.circular(10),
                child: SizedBox(
                  width: 110,
                  height: 72,
                  child: AppImage(url: video.thumbnailUrl, icon: Icons.play_circle_outline),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(video.title, style: const TextStyle(fontFamily: FontsStyle.semiBold, fontSize: 14)),
                    const SizedBox(height: 4),
                    Text(
                      [video.categoryName, video.durationLabel].where((part) => part != null && part.isNotEmpty).join(' · '),
                      style: const TextStyle(color: ColorConstant.bodyTextCl, fontSize: 12),
                    ),
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                          decoration: BoxDecoration(
                            color: video.isPaid ? ColorConstant.primaryLightCl : ColorConstant.primaryMintCl,
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: Text(
                            video.isPaid ? 'Paid' : 'Free',
                            style: const TextStyle(fontSize: 11, fontFamily: FontsStyle.medium, color: ColorConstant.white),
                          ),
                        ),
                        if (video.locked) ...[
                          const SizedBox(width: 6),
                          const Icon(Icons.lock, size: 16, color: ColorConstant.white),
                        ],
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class VideoPoster extends StatelessWidget {
  const VideoPoster({super.key, required this.video, required this.onTap, this.width = 132});

  final VideoItem video;
  final VoidCallback onTap;
  final double width;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: width,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            ClipRRect(
              borderRadius: BorderRadius.circular(12),
              child: AspectRatio(
                aspectRatio: 2 / 3,
                child: Stack(
                  fit: StackFit.expand,
                  children: [
                    AppImage(url: video.thumbnailUrl, icon: Icons.movie_outlined),
                    const DecoratedBox(
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                          colors: [Colors.transparent, Color(0xCC010A0F)],
                        ),
                      ),
                    ),
                    Positioned(
                      left: 8,
                      top: 8,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(
                          color: video.locked ? ColorConstant.primaryCl : const Color(0xCC10181D),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(
                          video.locked ? 'LOCK' : (video.isPaid ? 'PAID' : 'FREE'),
                          style: const TextStyle(color: ColorConstant.white, fontSize: 10, fontFamily: FontsStyle.bold),
                        ),
                      ),
                    ),
                    Positioned(
                      left: 8,
                      right: 8,
                      bottom: 8,
                      child: Text(
                        video.title,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(color: ColorConstant.white, fontFamily: FontsStyle.semiBold, fontSize: 12),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
