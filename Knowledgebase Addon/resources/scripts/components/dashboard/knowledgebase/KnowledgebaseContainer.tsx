import React, { useEffect, useState } from 'react';
import PageContentBlock from '@/components/elements/PageContentBlock';
import tw from 'twin.macro';
import FlashMessageRender from '@/components/FlashMessageRender';
import Spinner from '@/components/elements/Spinner';
import useFlash from '@/plugins/useFlash';
import useSWR from 'swr';
import { Link } from 'react-router-dom';
import Button from '@/components/elements/Button';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import MessageBox from '@/components/MessageBox';

import getCategories from '@/api/knowledgebase/getCategories';

export interface CategoriesResponse {
    categories: any[];
    $questions: any[];
    $settings: any[];
}

export default () => {
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error } = useSWR<CategoriesResponse>([ '/knowledgebase' ], () => getCategories());


    useEffect(() => {
        if (!error) {
            clearFlashes('knowledgebase');
        } else {
            clearAndAddHttpError({ key: 'knowledgebase', error });
        }
    });

    return (
        <PageContentBlock title={'Knowledgebase Categories'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'knowledgebase'} css={tw`mb-4`} />
            </div>
            {!data ?
                <div css={tw`w-full`}>
                    <Spinner size={'large'} centered />
                </div>
                :
                <>
                    {data.categories.length < 1 ?
                      <div css={tw`w-full`}>
                            <MessageBox type="info" title="Info">
                                There are no category.
                            </MessageBox>
                        </div>
                        :
                        (data.categories.map((item, key) => (
                            <div css={tw`w-full lg:w-4/12 lg:pl-4`} key={key}>
                                <TitledGreyBox title={item.name}>
                                    <div css={tw`px-1 py-2 justify-center`}>
                                        {item.description}
                                        <br></br>
                                        <div css={tw`flex justify-end`}>
                                            <Link to={`/knowledgebase/list/${item.id}`}>
                                                <Button size={'xsmall'} css={'float: right;'} color={'primary'}>View</Button>
                                            </Link>
                                        </div>
                                    </div>
                                </TitledGreyBox>
                                <br></br>
                            </div>
                        )))
                    }
                </>
            }
        </PageContentBlock>
    );
};
