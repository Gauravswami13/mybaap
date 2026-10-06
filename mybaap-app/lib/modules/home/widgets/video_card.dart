import 'package:flutter/material.dart';
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
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
      color: ColorConstant.white,
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(14),
        side: const BorderSide(color: ColorConstant.cardBorderCl),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(14),
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
                  child: video.thumbnailUrl == null
                      ? const ColoredBox(color: ColorConstant.primaryMintCl)
                      : Image.network(
                          video.thumbnailUrl!,
                          fit: BoxFit.cover,
                          errorBuilder: (_, _, _) => const ColoredBox(color: ColorConstant.primaryMintCl),
                        ),
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
                            style: const TextStyle(fontSize: 11, fontFamily: FontsStyle.medium, color: ColorConstant.primaryDarkCl),
                          ),
                        ),
                        if (video.locked) ...[
                          const SizedBox(width: 6),
                          const Icon(Icons.lock, size: 16, color: ColorConstant.primaryDarkCl),
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
