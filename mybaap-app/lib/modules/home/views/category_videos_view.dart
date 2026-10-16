import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/green_app_bar.dart';
import 'package:mybaap/core/common_widgets/state_message.dart';
import 'package:mybaap/modules/home/models/catalog_models.dart';
import 'package:mybaap/modules/home/providers/home_provider.dart';
import 'package:mybaap/modules/home/widgets/video_card.dart';
import 'package:mybaap/routes/app_routes.dart';
import 'package:provider/provider.dart';

class CategoryVideosView extends StatelessWidget {
  const CategoryVideosView({super.key, required this.category});

  final CategoryItem category;

  @override
  Widget build(BuildContext context) {
    final home = context.watch<HomeProvider>();
    final showing = home.activeCategoryId == category.id;
    if (!showing || (!home.categoryLoading && !home.categoryReady)) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        home.loadCategory(category.id);
      });
    }
    final loading = !showing || home.categoryLoading || !home.categoryReady;
    return Scaffold(
      backgroundColor: ColorConstant.bgCl,
      appBar: GreenAppBar(title: category.name),
      body: loading
          ? const Center(child: CircularProgressIndicator(color: ColorConstant.primaryCl))
          : home.categoryError != null && home.categoryVideos.isEmpty
              ? StateMessage(
                  title: 'Could not load videos',
                  subtitle: home.categoryError,
                  onRetry: () => home.loadCategory(category.id, force: true),
                )
              : home.categoryVideos.isEmpty
                  ? const StateMessage(title: 'No videos yet')
                  : RefreshIndicator(
                      color: ColorConstant.primaryCl,
                      onRefresh: () => home.loadCategory(category.id, force: true),
                      child: GridView.builder(
                        padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
                        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: 3,
                          childAspectRatio: 0.62,
                          crossAxisSpacing: 10,
                          mainAxisSpacing: 10,
                        ),
                        itemCount: home.categoryVideos.length,
                        itemBuilder: (context, index) {
                          final video = home.categoryVideos[index];
                          return VideoPoster(
                            width: double.infinity,
                            video: video,
                            onTap: () async {
                                  await Navigator.pushNamed(context, AppRoutes.videoDetail, arguments: video);
                                  if (!context.mounted) return;
                                  await context.read<HomeProvider>().loadCategory(category.id, force: true);
                                },
                          );
                        },
                      ),
                    ),
    );
  }
}
